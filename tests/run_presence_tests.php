<?php

/**
 * PRESENCE MASTER TEST SUITE RUNNER
 * Consolidates all verification into 6 essential pillars:
 * - CORE
 * - ATTENDANCE
 * - HR
 * - SECURITY
 * - MODULES
 * - SYSTEM
 */

$suites = [
    'CORE'       => 'tests/CoreTest.php',
    'ATTENDANCE' => 'tests/AttendanceTest.php',
    'HR'         => 'tests/HRWorkflowTest.php',
    'SECURITY'   => 'tests/SecurityModuleTest.php',
    'MODULES'    => 'tests/SecurityModuleTest.php',
    'SYSTEM'     => 'tests/SystemHostingTest.php',
];

$results = [];

echo "\n========================================================================\n";
echo "   PRESENCE UNIVERSAL HR — MASTER TEST RUNNER                           \n";
echo "========================================================================\n\n";

foreach ($suites as $name => $file) {
    if ($name === 'MODULES') {
        // MODULES is covered within SecurityModuleTest
        $results['MODULES'] = $results['SECURITY'] ?? false;
        continue;
    }

    echo ">>> RUNNING SUITE: {$name} ({$file}) ...\n";
    $exitCode = 0;
    passthru("php {$file}", $exitCode);
    $results[$name] = ($exitCode === 0);
}

// Final Report Format
echo "\n========================================================================\n";
echo "   PRESENCE MASTER TEST SUITE RESULTS                                  \n";
echo "========================================================================\n";

$allPassed = true;
foreach ($results as $name => $passed) {
    if (!$passed) $allPassed = false;
    $statusText = $passed ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m";
    printf("  %-15s %s\n", $name, $statusText);
}

echo "========================================================================\n";

if ($allPassed) {
    echo ">>> ALL PRESENCE SUITES PASSED (100% SUCCESS) <<<\n\n";
    exit(0);
} else {
    echo ">>> SOME SUITES FAILED <<<\n\n";
    exit(1);
}
