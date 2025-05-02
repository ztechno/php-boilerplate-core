<?php 

use Core\Database;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$db = new Database;

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Jenis file yang diizinkan
    $allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        set_flash_msg(['error'=> 'Silakan unggah file Excel']);
    }
    else
    {

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if (in_array($fileExtension, ['xls', 'xlsx'])) {
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $sheet = $spreadsheet->getActiveSheet();
    
            foreach ($sheet->getRowIterator(2) as $row) {
                $name = $sheet->getCell('A' . $row->getRowIndex())->getFormattedValue();
                $email = $sheet->getCell('B' . $row->getRowIndex())->getFormattedValue();
                
                // check username
                $user = $db->single('users', ['username' => $email]);
                if(!$user)
                {
                    $user = $db->insert('users', [
                        'name' => $name,
                        'username' => $email,
                        'password' => md5(123456)
                    ]);

                    $db->insert('user_roles', [
                        'user_id' => $user->id,
                        'role_id' => env('USER_ROLE_ID')
                    ]);
                }
            }
        }
           
        set_flash_msg(['success'=> 'Data berhasil di Import']);
    }
    
} else {
    set_flash_msg(['error'=> 'Silakan unggah file Excel atau CSV.']);
}

header('Location: '.routeTo('crud/index',['table' => 'users']));
die();
