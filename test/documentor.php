<?php
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';

$generator = Faker\Factory::create();
$generator->seed(1);
$documentor = new Faker\Documentor($generator);
echo 'Starting' . PHP_EOL;
$getFormatterArrayTimerStart = microtime(true);
$formatters = $documentor->getFormatters();
$totalTime = microtime(true) - $getFormatterArrayTimerStart;

?>
<?php foreach ($formatters as $provider => $formatter): ?>

### `<?php echo $provider ?>`

<?php foreach ($formatter as $f => $example): ?>
    <?php echo str_pad($f, 23) ?><?php if ($example): ?> // <?php echo $example ?> <?php endif; ?>

<?php endforeach; ?>
<?php endforeach;
echo PHP_EOL;
echo 'Documented formatters in ' . $totalTime . ' seconds';
echo PHP_EOL;
exit(0);
