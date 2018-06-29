var tests = [
    // "example",
    "GameTest",
    "MapTest",
    "MotionTest",
    "PositionTest",
    "VectorTest",
    "KRACHZTest",
    // "UserFactoryTest",
];

//tests = ["VectorTest"];

for (var i = 0; i < tests.length; i++) {
    var testName = tests[i];
    console.log("Run", testName);
    exports[testName] = require('./' + testName);
}