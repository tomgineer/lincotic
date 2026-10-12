<?php

namespace Tests\Unit;

use App\Services\CronTasks\CheckRobotsTxt;
use CodeIgniter\Test\CIUnitTestCase;

final class CheckRobotsTxtTest extends CIUnitTestCase
{
    private string $outputPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outputPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'lincotic-robots-'
            . bin2hex(random_bytes(8));

        mkdir($this->outputPath, 0777, true);
    }

    protected function tearDown(): void
    {
        $robotsPath = $this->outputPath . DIRECTORY_SEPARATOR . 'robots.txt';

        if (is_file($robotsPath)) {
            unlink($robotsPath);
        }

        if (is_dir($this->outputPath)) {
            rmdir($this->outputPath);
        }

        parent::tearDown();
    }

    public function testCreatesRobotsFileWithoutSitemap(): void
    {
        (new CheckRobotsTxt($this->outputPath))->run();

        $robots = file_get_contents($this->outputPath . DIRECTORY_SEPARATOR . 'robots.txt');

        $this->assertIsString($robots);
        $this->assertStringContainsString('User-agent: *', $robots);
        $this->assertStringContainsString('Allow: /', $robots);
        $this->assertStringContainsString('Disallow: /edit/', $robots);
        $this->assertStringContainsString('Disallow: /go/', $robots);
        $this->assertStringContainsString('Disallow: /login', $robots);
        $this->assertStringContainsString('Disallow: /logout', $robots);
        $this->assertStringNotContainsString('Sitemap:', $robots);
    }

    public function testLeavesValidRobotsFileUnchanged(): void
    {
        $path = $this->outputPath . DIRECTORY_SEPARATOR . 'robots.txt';
        $contents = implode(PHP_EOL, [
            'User-agent: *',
            'Allow: /',
            'Disallow: /edit/',
            'Disallow: /go/',
            'Disallow: /login',
            'Disallow: /logout',
            '# Keep this custom comment.',
            '',
        ]);
        file_put_contents($path, $contents);

        (new CheckRobotsTxt($this->outputPath))->run();

        $this->assertSame($contents, file_get_contents($path));
    }

    public function testReplacesInvalidRobotsFile(): void
    {
        $path = $this->outputPath . DIRECTORY_SEPARATOR . 'robots.txt';
        file_put_contents($path, "User-agent: *\nDisallow:\n");

        (new CheckRobotsTxt($this->outputPath))->run();

        $robots = file_get_contents($path);

        $this->assertIsString($robots);
        $this->assertStringContainsString('Disallow: /edit/', $robots);
        $this->assertStringContainsString('Disallow: /go/', $robots);
        $this->assertStringNotContainsString('Sitemap:', $robots);
    }
}
