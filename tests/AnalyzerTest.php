<?php declare(strict_types=1);

require('vendor/autoload.php');
require('src/Analyzer.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpScatterplot\Analyzer;

final class AnalyzerTest extends TestCase
{
    public function test_isValid_can_return_bool_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => false, ],
            ['data' => true, 'expect' => false, ],
            ['data' => false, 'expect' => false, ],
            ['data' => 0, 'expect' => false, ],
            ['data' => 1.2, 'expect' => false, ],
            ['data' => '0', 'expect' => false, ],
            ['data' => [], 'expect' => false, ],
            ['data' => [[]], 'expect' => false, ],
            ['data' => [null], 'expect' => false, ],
            ['data' => [true], 'expect' => false, ],
            ['data' => [false], 'expect' => false, ],
            ['data' => [0], 'expect' => true, ],
            ['data' => [1.2], 'expect' => true, ],
            ['data' => ['0'], 'expect' => false, ],
            ['data' => [0,1.2], 'expect' => true, ],
            ['data' => [0,1.2,'3'], 'expect' => false, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->isValid($case['data']));
        }
    }

    public function test_mean_can_retrun_mean_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '0', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['0'], 'expect' => null, ],
            ['data' => [1], 'expect' => 1, ],
            ['data' => [1,2], 'expect' => 1.5, ],
            ['data' => [1,2,'3'], 'expect' => null, ],
            ['data' => [1.5,2.5,3.5], 'expect' => 2.5, ],
            ['data' => [1,2,3], 'expect' => 2, ],
            ['data' => [1.5,2.5,3.5,4.5], 'expect' => 3.0, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->mean($case['data']));
        }
    }

    public function test_variance_can_return_variance_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '0', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => [0], 'expect' => 0, ],
            ['data' => [1.2], 'expect' => 0.0, ],
            ['data' => ['0'], 'expect' => null, ],
            ['data' => [1], 'expect' => 0, ],
            ['data' => [1,2], 'expect' => 0.25, ],
            ['data' => [-1,1,3,5], 'expect' => 5, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->variance($case['data']));
        }
    }

    public function test_covariance_can_return_covariance_correctly(): void
    {
        $cases = [
            ['x' => null, 'y' => [1], 'expect' => null, ],
            ['x' => true, 'y' => [1], 'expect' => null, ],
            ['x' => false, 'y' => [1], 'expect' => null, ],
            ['x' => 0, 'y' => [1], 'expect' => null, ],
            ['x' => 1.2, 'y' => [1], 'expect' => null, ],
            ['x' => '0', 'y' => [1], 'expect' => null, ],
            ['x' => [], 'y' => [1], 'expect' => null, ],
            ['x' => [null], 'y' => [1], 'expect' => null, ],
            ['x' => [true], 'y' => [1], 'expect' => null, ],
            ['x' => [false], 'y' => [1], 'expect' => null, ],
            ['x' => ['1'], 'y' => [1], 'expect' => null, ],
            ['x' => [[]], 'y' => [1], 'expect' => null, ],
            ['x' => [1], 'y' => null, 'expect' => null, ],
            ['x' => [1], 'y' => true, 'expect' => null, ],
            ['x' => [1], 'y' => false, 'expect' => null, ],
            ['x' => [1], 'y' => 0, 'expect' => null, ],
            ['x' => [1], 'y' => 1.2, 'expect' => null, ],
            ['x' => [1], 'y' => '1', 'expect' => null, ],
            ['x' => [1], 'y' => [], 'expect' => null, ],
            ['x' => [1], 'y' => [null], 'expect' => null, ],
            ['x' => [1], 'y' => [true], 'expect' => null, ],
            ['x' => [1], 'y' => [false], 'expect' => null, ],
            ['x' => [1], 'y' => ['1'], 'expect' => null, ],
            ['x' => [1], 'y' => [[]], 'expect' => null, ],
            ['x' => [1], 'y' => [1], 'expect' => 0, ],
            ['x' => [1.5], 'y' => [1.5], 'expect' => 0.0, ],
            ['x' => [1,2], 'y' => [1], 'expect' => null, ],
            ['x' => [1,2,3], 'y' => [4,5,6], 'expect' => 2 / 3, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->covariance($case['x'], $case['y']));
        }
    }

    public function test_standardDeviation_can_return_standard_deviation_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '1', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['1'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [1], 'expect' => 0.0, ],
            ['data' => [1,2], 'expect' => 0.5, ],
            ['data' => [1,2,'3'], 'expect' => null, ],
            ['data' => [-2,-1,0,1,2], 'expect' => sqrt(2), ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->standardDeviation($case['data']));
        }
    }

    public function test_correlationCoefficient_can_return_correlation_coefficient_correctly(): void
    {
        $cases = [
            ['x' => null, 'y' => null, 'expect' => null, ],
            ['x' => null, 'y' => [1], 'expect' => null, ],
            ['x' => true, 'y' => [1], 'expect' => null, ],
            ['x' => false, 'y' => [1], 'expect' => null, ],
            ['x' => 0, 'y' => [1], 'expect' => null, ],
            ['x' => 1.2, 'y' => [1], 'expect' => null, ],
            ['x' => '1', 'y' => [1], 'expect' => null, ],
            ['x' => [], 'y' => [1], 'expect' => null, ],
            ['x' => [null], 'y' => [1], 'expect' => null, ],
            ['x' => [true], 'y' => [1], 'expect' => null, ],
            ['x' => [false], 'y' => [1], 'expect' => null, ],
            ['x' => ['1'], 'y' => [1], 'expect' => null, ],
            ['x' => [[]], 'y' => [1], 'expect' => null, ],
            ['x' => [1], 'y' => null, 'expect' => null, ],
            ['x' => [1], 'y' => true, 'expect' => null, ],
            ['x' => [1], 'y' => false, 'expect' => null, ],
            ['x' => [1], 'y' => 0, 'expect' => null, ],
            ['x' => [1], 'y' => 1.2, 'expect' => null, ],
            ['x' => [1], 'y' => '1', 'expect' => null, ],
            ['x' => [1], 'y' => [], 'expect' => null, ],
            ['x' => [1], 'y' => [null], 'expect' => null, ],
            ['x' => [1], 'y' => [true], 'expect' => null, ],
            ['x' => [1], 'y' => [false], 'expect' => null, ],
            ['x' => [1], 'y' => ['0'], 'expect' => null, ],
            ['x' => [1], 'y' => [[]], 'expect' => null, ],
            ['x' => [1], 'y' => [1], 'expect' => null, ],
            ['x' => [1,2], 'y' => [1], 'expect' => null, ],
            ['x' => [1,2], 'y' => [1,2], 'expect' => 1.0, ],
            ['x' => [1,2,3,4], 'y' => [3,1,4,2], 'expect' => 0.0, ],
            ['x' => [1,2], 'y' => [2,1], 'expect' => -1.0, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->correlationCoefficient($case['x'], $case['y']));
        }
    }

    public function test_regressionLineFormula_can_return_values_correctly(): void
    {
        $cases = [
            ['x' => null, 'y' => [1], 'expect' => null, ],
            ['x' => true, 'y' => [1], 'expect' => null, ],
            ['x' => false, 'y' => [1], 'expect' => null, ],
            ['x' => 0, 'y' => [1], 'expect' => null, ],
            ['x' => 1.2, 'y' => [1], 'expect' => null, ],
            ['x' => '1', 'y' => [1], 'expect' => null, ],
            ['x' => [], 'y' => [1], 'expect' => null, ],
            ['x' => [null], 'y' => [1], 'expect' => null, ],
            ['x' => [true], 'y' => [1], 'expect' => null, ],
            ['x' => [false], 'y' => [1], 'expect' => null, ],
            ['x' => [0], 'y' => [1], 'expect' => null, ],
            ['x' => [1.2], 'y' => [1], 'expect' => null, ],
            ['x' => ['1'], 'y' => [1], 'expect' => null, ],
            ['x' => [[]], 'y' => [1], 'expect' => null, ],
            ['x' => [1], 'y' => null, 'expect' => null, ],
            ['x' => [1], 'y' => true, 'expect' => null, ],
            ['x' => [1], 'y' => false, 'expect' => null, ],
            ['x' => [1], 'y' => 0, 'expect' => null, ],
            ['x' => [1], 'y' => 1.2, 'expect' => null, ],
            ['x' => [1], 'y' => '1', 'expect' => null, ],
            ['x' => [1], 'y' => [], 'expect' => null, ],
            ['x' => [1], 'y' => [null], 'expect' => null, ],
            ['x' => [1], 'y' => [true], 'expect' => null, ],
            ['x' => [1], 'y' => [false], 'expect' => null, ],
            ['x' => [1], 'y' => ['1'], 'expect' => null, ],
            ['x' => [1], 'y' => [[]], 'expect' => null, ],
            ['x' => [1], 'y' => [1], 'expect' => null, ],
            ['x' => [1,2], 'y' => [1,2], 'expect' => ['a' => 1.0, 'b' => 0.0], ],
            ['x' => [1,2], 'y' => [1,2,3], 'expect' => null, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->regressionLineFormula($case['x'], $case['y']));
        }
    }

    public function test_getUcl_can_return_ucl_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '1', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['1'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [1], 'expect' => 1.0, ],
            ['data' => [1,2], 'expect' => 3.5, ],
            ['data' => [1,2,3,4,5], 'expect' => 9.0, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->getUcl($case['data']));
        }
    }

    public function test_getLcl_can_return_lcl_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '1', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['1'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [1], 'expect' => 1.0, ],
            ['data' => [1,2], 'expect' => -0.5, ],
            ['data' => [1,2,3,4,5], 'expect' => -3.0, ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->getLcl($case['data']));
        }
    }

    public function test_outliers_can_return_outliers_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '1', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['1'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [1], 'expect' => [], ],
            ['data' => [1,2], 'expect' => [], ],
            ['data' => [1,2,3,4,5,100], 'expect' => [100], ],
            ['data' => [1,90,92,94,96,98,100], 'expect' => [1], ],
            ['data' => [1,50,51,52,53,54,55,100], 'expect' => [1,100], ],
        ];
        $a = new Analyzer();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $a->outliers($case['data']));
        }
    }
}
