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
            + "hasTeamSwapGameStarted,"
            + "swapRegisteredTeamStateBeforeGame,"
            + "getInningCorrectionRollbackDepth,"
            + "normalizeStarterImportText,"
            + "parseStarterPostText,"
            + "captureGameHistorySnapshot,"
            + "isInningShareEveryInningEnabled,"
            + "setInningShareEveryInning,"
            + "getInningShareFlowMode,"
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

test("header exposes direct live import and keeps secondary actions in one menu", () => {
    const html = fs.readFileSync(INDEX_PATH, "utf8");
    assert.match(html, /class="header-live-import-btn"[^>]+onclick="openOnePlateAppearanceImport\(\)"[^>]*>実況入力<\/button>/);
    assert.match(html, /id="headerMenuPanel"[^>]+aria-hidden="true"/);
    assert.match(html, /id="workspaceToggleBtn"[^>]+class="workspace-toggle-btn header-menu-action"/);
    assert.match(html, /id="inputLayoutToggleBtn"[^>]+class="input-layout-toggle-btn header-menu-action"/);
    assert.match(html, /onclick="openHeaderMenuDetails\('playerRosterDetails'\)"[^>]*>[\s\S]*?チーム・選手<\/span>/);
    assert.match(html, /onclick="openHeaderMenuDetails\('gameSettingsDetails'\)"[^>]*>[\s\S]*?試合設定<\/span>/);
    assert.match(html, /id="playerRosterDetails"[^>]+data-header-menu-section/);
    assert.match(html, /id="gameSettingsDetails"[^>]+data-header-menu-section/);
    assert.match(html, /id="gameInfoShareEveryInning"[^>]+onchange="setInningShareEveryInning\(this\.checked\)"/);
    assert.match(html, /function openOnePlateAppearanceImport\(\)[\s\S]*showOptionPanel\("x", \{ onePlateDirect: true \}\);/);
});

test("every-inning X sharing is stored with the game and opens the draft directly", () => {
    const api = loadScorebookTestApi();
    assert.equal(api.isInningShareEveryInningEnabled(), false);
    assert.equal(api.getInningShareFlowMode(), "prompt");

    api.setInningShareEveryInning(true);
    assert.equal(api.isInningShareEveryInningEnabled(), true);
    assert.equal(api.getInningShareFlowMode(), "draft");
    assert.equal(api.getInningShareFlowMode({ showShareDialog: false }), "skip");
    assert.equal(api.captureGameHistorySnapshot().state.shareEveryInning, true);
});

test("Ninja AdMax is isolated in a sandboxed srcdoc iframe", () => {
    const html = fs.readFileSync(INDEX_PATH, "utf8");
    const mountSource = html.match(/function mountAdGateAdProvider\(placement = "inning"\) \{[\s\S]*?\n        \}/)?.[0] || "";

    assert.match(mountSource, /document\.createElement\("iframe"\)/);
    assert.match(mountSource, /frame\.setAttribute\("sandbox", "allow-scripts allow-popups allow-popups-to-escape-sandbox"\)/);
    assert.match(mountSource, /frame\.srcdoc =/);
    assert.match(mountSource, /frame\.addEventListener\("load"/);
    assert.match(mountSource, /slot\.dataset\.loaded === "true"/);
    assert.match(mountSource, /placement !== "inning" \|\| AD_GATE_CONFIG\.provider !== "ninja_admax"/);
    assert.match(mountSource, /frame\.dataset\.loaded !== "true"[\s\S]*renderAdGateFallback\(slot, placement\)/);
    assert.doesNotMatch(mountSource, /allow-same-origin/);
    assert.doesNotMatch(mountSource, /document\.createElement\("script"\)/);

    const skipSource = html.match(/function shouldSkipAdGate\(options = \{\}\) \{[\s\S]*?\n        \}/)?.[0] || "";
    assert.match(skipSource, /AD_GATE_CONFIG\.skipWhenOffline && !isNetworkOnline\(\)/);
});

test("AI photo response imports game details and both complete lineups", () => {
    const api = loadScorebookTestApi();
    const parsed = api.parseStarterPostText([
        "大会名：不明",
        "年月日：不明",
        "球場：不明",
        "球審：不明",
        "一塁審：不明",
        "二塁審：不明",
        "三塁審：不明",
        "左線審：不明",
        "右線審：不明",
        "先攻・智弁和歌山スタメン：8長友悠成、3荒井優聖、9井本陽太、2山田凛虎、4楠本龍生、DH松本虎太郎、6川原一将、7山下晃平、5黒川梨太郎　投手・#1和気匠太",
        "後攻・横浜スタメン：3小野舜友、4田島陽翔、6池田聖摩、5川上慧、7江坂佳史、9千島大翼、DH安食琥太郎、2脇山魁音、8小林大雅　投手・#1織田翔希"
    ].join("\n"));

    assert.equal(parsed.teams.top.teamName, "智弁和歌山");
    assert.deepEqual(
        JSON.parse(JSON.stringify(parsed.teams.top.entries.map((entry) => entry.name))),
        ["長友悠成", "荒井優聖", "井本陽太", "山田凛虎", "楠本龍生", "松本虎太郎", "川原一将", "山下晃平", "黒川梨太郎"]
    );
    assert.equal(parsed.teams.top.pitcher, "和気匠太");
    assert.equal(parsed.teams.top.pitcherNumber, "1");
    assert.equal(parsed.teams.bottom.teamName, "横浜");
    assert.deepEqual(
        JSON.parse(JSON.stringify(parsed.teams.bottom.entries.map((entry) => entry.name))),
        ["小野舜友", "田島陽翔", "池田聖摩", "川上慧", "江坂佳史", "千島大翼", "安食琥太郎", "脇山魁音", "小林大雅"]
    );
    assert.equal(parsed.teams.bottom.pitcher, "織田翔希");
    assert.equal(parsed.teams.bottom.pitcherNumber, "1");
});

test("AI photo response tolerates copied markdown and Unicode line separators", () => {
    const api = loadScorebookTestApi();
    const decorated = [
        "```text",
        "**大会名：不明**",
        "- **球審：不明**",
        "- **先攻・智弁和歌山スタメン：**8長友悠成、3荒井優聖、9井本陽太、2山田凛虎、4楠本龍生、DH松本虎太郎、6川原一将、7山下晃平、5黒川梨太郎　投手・#1和気匠太",
        "- **後攻・横浜スタメン：**3小野舜友、4田島陽翔、6池田聖摩、5川上慧、7江坂佳史、9千島大翼、DH安食琥太郎、2脇山魁音、8小林大雅　投手・#1織田翔希",
        "```"
    ].join("\u2028");
    const parsed = api.parseStarterPostText(decorated);

    assert.equal(parsed.teams.top.entries.length, 9);
    assert.equal(parsed.teams.top.entries[0].name, "長友悠成");
    assert.equal(parsed.teams.bottom.entries.length, 9);
    assert.equal(parsed.teams.bottom.entries[8].name, "小林大雅");
});

test("registered teams can be swapped together before the first play", () => {
    const api = loadScorebookTestApi();
    api.state.teamNames = { top: "A高校", bottom: "B高校" };
    api.state.playerNames = {
        top: ["A1", ...Array(8).fill("")],
        bottom: ["B1", ...Array(8).fill("")]
    };
    api.state.playerNumbers = {
        top: ["1", ...Array(8).fill("")],
        bottom: ["11", ...Array(8).fill("")]
    };
    api.state.playerPositions = {
        top: ["6", ...Array(8).fill("")],
        bottom: ["8", ...Array(8).fill("")]
    };
    api.state.gameInfo = api.normalizeGameInfo({
        pitchers: { top: "A投手", bottom: "B投手" },
        pitcherNumbers: { top: "10", bottom: "20" },
        benches: {
            firstBaseSideTeamId: "team_top",
            thirdBaseSideTeamId: "team_bottom"
        }
    });

    assert.equal(api.hasTeamSwapGameStarted(), false);
    api.swapRegisteredTeamStateBeforeGame();

    assert.equal(api.state.teamNames.top, "B高校");
    assert.equal(api.state.teamNames.bottom, "A高校");
    assert.equal(api.state.playerNames.top[0], "B1");
    assert.equal(api.state.playerNumbers.top[0], "11");
    assert.equal(api.state.playerPositions.top[0], "8");
    assert.equal(api.state.gameInfo.pitchers.top, "B投手");
    assert.equal(api.state.gameInfo.pitcherNumbers.top, "20");
    assert.equal(api.state.gameInfo.benches.firstBaseSideTeamId, "team_bottom");
    assert.equal(api.state.gameInfo.benches.thirdBaseSideTeamId, "team_top");
    assert.equal(api.state.activeHalf, "top");
});

test("registered team swapping is blocked after scoring input starts", () => {
    const api = loadScorebookTestApi();
    api.state.pitches = ["B"];
    assert.equal(api.hasTeamSwapGameStarted(), true);

    api.state.pitches = [];
    api.state.scoreSheets = {
        top: api.createEmptyScoreSheet(),
        bottom: api.createEmptyScoreSheet()
    };
    api.state.scoreSheets.top[0][0] = { result: "7" };
    assert.equal(api.hasTeamSwapGameStarted(), true);
});

test("inning correction rollback also removes an accidental third strike", () => {
    const api = loadScorebookTestApi();
    const beforeThirdStrike = {
        state: {
            activeHalf: "top",
            selectedSheetRow: 2,
            selectedSheetCol: 0,
            pitches: ["calledStrike", "foul"],
            result: "",
            strikes: 2
        }
    };
    const beforeSave = {
        state: {
            activeHalf: "top",
            selectedSheetRow: 2,
            selectedSheetCol: 0,
            pitches: ["calledStrike", "foul", "calledStrike"],
            result: "K",
            strikes: 3
        }
    };

    assert.equal(api.getInningCorrectionRollbackDepth([beforeThirdStrike, beforeSave]), 2);
});

test("inning correction rollback keeps non-strikeout input editable", () => {
    const api = loadScorebookTestApi();
    const beforeResult = {
        state: {
            activeHalf: "bottom",
            selectedSheetRow: 4,
            selectedSheetCol: 3,
            pitches: ["ball"],
            result: "",
            strikes: 0
        }
    };
    const beforeSave = {
        state: {
            activeHalf: "bottom",
            selectedSheetRow: 4,
            selectedSheetCol: 3,
            pitches: ["ball"],
            result: "8",
            strikes: 0
        }
    };

    assert.equal(api.getInningCorrectionRollbackDepth([beforeResult, beforeSave]), 1);
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

test("pitch counts stay with the relief pitcher when the batting order wraps from ninth to first", () => {
    const api = loadScorebookTestApi();
    api.state.gameInfo = api.normalizeGameInfo({
        pitchers: {
            top: "先発",
            bottom: "相手先発"
        },
        pitcherChanges: {
            top: [{
                name: "救援",
                inning: "9",
                entryCol: 8,
                battingOrder: 9
            }],
            bottom: []
        }
    });
    api.state.scoreSheets = {
        top: api.createEmptyScoreSheet(),
        bottom: api.createEmptyScoreSheet()
    };
    api.state.inningLabelsByHalf = {
        top: Array.from({ length: 18 }, (_, index) => index + 1),
        bottom: Array.from({ length: 18 }, (_, index) => index + 1)
    };
    api.state.activeHalf = "top";
    api.state.selectedSheetRow = 0;
    api.state.selectedSheetCol = 0;
    api.state.scoreSheets.bottom[7][8] = {
        pitches: ["B", "S"],
        result: "6",
        selectedFielder: "6"
    };
    api.state.scoreSheets.bottom[8][8] = {
        pitches: ["B"],
        result: "9",
        selectedFielder: "9"
    };
    api.state.scoreSheets.bottom[0][8] = {
        pitches: ["B", "S", "F"],
        result: "8",
        selectedFielder: "8"
    };

    assert.deepEqual(
        JSON.parse(JSON.stringify(api.calculatePitcherStatsForTeam("top"))),
        [
            { pitchCount: 3, battersFaced: 1 },
            { pitchCount: 6, battersFaced: 2 }
        ]
    );
});

test("a late relief change after batting around does not claim earlier batters in the inning", () => {
    const api = loadScorebookTestApi();
    api.state.gameInfo = api.normalizeGameInfo({
        pitchers: {
            top: "相手先発",
            bottom: "先発"
        },
        pitcherChanges: {
            top: [],
            bottom: [{
                name: "救援",
                inning: "6",
                entryCol: 5,
                battingOrder: 5
            }]
        }
    });
    api.state.scoreSheets = {
        top: api.createEmptyScoreSheet(),
        bottom: api.createEmptyScoreSheet()
    };
    api.state.inningLabelsByHalf = {
        top: [1, 2, 3, 4, 5, 6, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],
        bottom: Array.from({ length: 18 }, (_, index) => index + 1)
    };
    api.state.activeHalf = "bottom";
    api.state.selectedSheetRow = 0;
    api.state.selectedSheetCol = 0;
    api.state.scoreSheets.top[4][4] = { result: "PO" };
    [5, 6, 7, 8, 0, 1, 2, 3, 4].forEach((row) => {
        api.state.scoreSheets.top[row][5] = {
            pitches: ["S"],
            result: "K"
        };
    });
    api.state.scoreSheets.top[5][6] = {
        pitches: ["S"],
        result: "K"
    };

    assert.deepEqual(
        JSON.parse(JSON.stringify(api.calculatePitcherStatsForTeam("bottom"))),
        [
            { pitchCount: 8, battersFaced: 8 },
            { pitchCount: 2, battersFaced: 2 }
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
