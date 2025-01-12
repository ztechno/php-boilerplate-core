<?php

use Core\Utility;
use Core\Database;

if (!isset($argv[2])) 
{
    echo "Module's name is empty\n";
    die;
}

if (!isset($argv[3])) 
{
    echo "Table's name is empty\n";
    die;
}

$moduleName  = $argv[2];
$tableName   = $argv[3];
$parent_path = Utility::parentPath();
$tableFieldsFile = $parent_path . 'modules/' . $moduleName . "/config/table-fields.php";
$tableFields = require $tableFieldsFile;

$db = new Database;
$db->query = "DESCRIBE $tableName";
$data = $db->exec('all');
$fields = [];
foreach($data as $d)
{
    if(in_array($d->Field, ['id','created_at','updated_at','created_by','updated_by'])) continue;

    $fields[$d->Field] = [
        'label' => $d->Field,
        'type' => 'text'
    ];
}

$tableFields = array_merge($tableFields, [
    $tableName => $fields
]);

$fieldData = "<?php

return ";
file_put_contents($tableFieldsFile, $fieldData . var_export($tableFields, 1) .";");