const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");
const test = require("node:test");
const vm = require("node:vm");

const ROOT = path.resolve(__dirname, "..");
const INDEX_PATH = path.join(ROOT, "index.html");

function createStorage() {
    const values = new Map();
    return {
        getItem(key) {
            return values.has(key) ? values.get(key) : null;
        },
        setItem(key, value) {
            values.set(key, String(value));
        },
        removeItem(key) {
            values.delete(key);
        },
        clear() {
            values.clear();
        }
    };
}

function createElementStub() {
    return {
        style: {},
        classList: {
            add() {},
            remove() {},
            toggle() {}
        },
        setAttribute() {},
        appendChild() {},
        click() {},
        remove() {}
    };
}

function loadScorebookTestApi() {
    const html = fs.readFileSync(INDEX_PATH, "utf8");
    const scripts = Array.from(html.matchAll(/<script(?:\s[^>]*)?>([\s\S]*?)<\/script>/gi), (match) => match[1]);
    const appScript = scripts.find((script) => script.includes("function getSnapshotPitchCount("));
    assert.ok(appScript, "scorebook application script was not found");

    const initializationMarker = "        state.scoreSheets = {";
    const initializationIndex = appScript.lastIndexOf(initializationMarker);
    assert.ok(initializationIndex > 0, "scorebook initialization marker was not found");

    const context = {
        console: {
            ...console,
            warn() {}
        },
        URL,
        URLSearchParams,
        Blob,
        TextEncoder,
        TextDecoder,
        atob: (value) => Buffer.from(value, "base64").toString("binary"),
        btoa: (value) => Buffer.from(value, "binary").toString("base64"),
        setTimeout: () => 0,
        clearTimeout() {},
        setInterval: () => 0,
        clearInterval() {},
        localStorage: createStorage(),
        sessionStorage: createStorage(),
        navigator: {
            userAgent: "node",
            clipboard: {
                async writeText() {}
            }
        },
        window: {
            addEventListener() {},
            matchMedia: () => ({
                matches: false,
                addEventListener() {}
            }),
            location: {
                search: "",
                href: "http://test/"
            },
            print() {}
        },
        document: {
            getElementById: () => null,
            querySelector: () => null,
            querySelectorAll: () => [],
            addEventListener() {},
            documentElement: {
                style: {
                    setProperty() {}
                }
            },
            body: {
                appendChild() {}
            },
            createElement: createElementStub
        }
    };
    context.globalThis = context;
    vm.createContext(context);
    vm.runInContext(
        appScript.slice(0, initializationIndex)
            + "\nglobalThis.__scorebookTestApi = {"
            + "state,"
            + "createEmptyScoreSheet,"
            + "normalizeGameInfo,"
            + "getSnapshotPitchCount,"
            + "calculatePitcherStatsForTeam,"
            + "saveGameToLocalStorage,"
            + "storage: localStorage,"
            + "setSaveFailureHandler(handler) { showGameSaveFailurePopup = handler; }"
            + "};",
        context
    );
    return context.__scorebookTestApi;
}

test("release ledger version matches the application version", () => {
    const html = fs.readFileSync(INDEX_PATH, "utf8");
    const versionFile = fs.readFileSync(path.join(ROOT, "docs", "VERSION"), "utf8").trim();
    const changelog = fs.readFileSync(path.join(ROOT, "docs", "CHANGELOG.md"), "utf8");
    const releasePosts = fs.readFileSync(path.join(ROOT, "docs", "RELEASE_POSTS.md"), "utf8");
    const appVersion = html.match(/const APP_VERSION = "([^"]+)";/)?.[1];
    const versionPattern = appVersion.replaceAll(".", "\\.");
    const datedHeadingPattern = `## v${versionPattern} \\[\\d{4}-\\d{2}-\\d{2}\\]`;
    assert.equal(versionFile, appVersion);
    assert.match(changelog, new RegExp(`^${datedHeadingPattern}`));
    assert.match(releasePosts, new RegExp(`^${datedHeadingPattern}`, "m"));
});

test("pitch counts include the ball put in play but not runner-only actions", () => {
    const api = loadScorebookTestApi();
    assert.equal(api.getSnapshotPitchCount({
        pitches: ["B", "S"],
        result: "6",
        selectedFielder: "6",
        fieldSequence: ["6", "3"]
    }), 3);
    assert.equal(api.getSnapshotPitchCount({
        pitches: [],
        result: "BK",
        runnerStatusEvents: [{ reason: "BK" }]
    }), 0);
});

test("pitch timing metadata recovers missing pitch entries and manual correction wins", () => {
    const api = loadScorebookTestApi();
    assert.equal(api.getSnapshotPitchCount({
        pitches: [],
        result: "7",
        selectedFielder: "7",
        fieldSequence: ["7"],
        runnerStatusEvents: [{ pitchIndex: 3 }]
    }), 4);
    assert.equal(api.getSnapshotPitchCount({
        pitches: ["S"],
        result: "K",
        playDetails: { pitchCountOverride: 7 }
    }), 7);
});

test("a declared intentional walk without pitch entries adds zero pitches", () => {
    const api = loadScorebookTestApi();
    assert.equal(api.getSnapshotPitchCount({
        pitches: [],
        result: "IB"
    }), 0);
    assert.equal(api.getSnapshotPitchCount({
        pitches: ["B", "B"],
        result: "IB"
    }), 2);
});

test("pitch counts are assigned to the pitcher active at each batting position", () => {
    const api = loadScorebookTestApi();
    api.state.gameInfo = api.normalizeGameInfo({
        pitchers: {
            top: "先発",
            bottom: "相手先発"
        },
        pitcherChanges: {
            top: [{
                name: "救援",
                inning: "2",
                entryCol: 1,
                battingOrder: 2
            }],
            bottom: []
        }
    });
    api.state.scoreSheets = {
        top: api.createEmptyScoreSheet(),
        bottom: api.createEmptyScoreSheet()
    };
    api.state.activeHalf = "top";
    api.state.selectedSheetRow = 0;
    api.state.selectedSheetCol = 0;
    api.state.scoreSheets.bottom[0][0] = {
        pitches: ["S"],
        result: "K"
    };
    api.state.scoreSheets.bottom[1][1] = {
        pitches: ["B", "S"],
        result: "6",
        selectedFielder: "6",
        fieldSequence: ["6", "3"]
    };

    assert.deepEqual(
        JSON.parse(JSON.stringify(api.calculatePitcherStatsForTeam("top"))),
        [
            { pitchCount: 1, battersFaced: 1 },
            { pitchCount: 3, battersFaced: 1 }
        ]
    );
});

test("an autosave failure offers one emergency export prompt until saving recovers", () => {
    const api = loadScorebookTestApi();
    let promptCount = 0;
    api.setSaveFailureHandler(() => {
        promptCount += 1;
    });
    api.state.scoreSheets = {
        top: api.createEmptyScoreSheet(),
        bottom: api.createEmptyScoreSheet()
    };

    api.storage.setItem = () => {
        throw new Error("quota exceeded");
    };
    api.saveGameToLocalStorage();
    api.saveGameToLocalStorage();
    assert.equal(promptCount, 1);

    api.storage.setItem = () => {};
    api.saveGameToLocalStorage();
    api.storage.setItem = () => {
        throw new Error("quota exceeded again");
    };
    api.saveGameToLocalStorage();
    assert.equal(promptCount, 2);
});
