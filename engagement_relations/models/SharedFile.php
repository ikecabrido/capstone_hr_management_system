<?php
namespace App\Models;

class SharedFile extends BaseModel
{
    public function createFile($fileName, $filePath, $fileSize, $fileType, $uploadedBy, $description)
    {
        $sql = 'INSERT INTO eer_shared_files (file_name, file_path, file_size, file_type, uploaded_by, description, created_at) 
                VALUES (:file_name, :file_path, :file_size, :file_type, :uploaded_by, :description, NOW())';
        
        $this->execute($sql, [
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'uploaded_by' => $uploadedBy,
            'description' => $description
        ]);
        
        return $this->db->lastInsertId();
    }

    public function getAllFiles()
    {
        $nameSql = $this->getEmployeeNameSql('e', 'uploader_name');
        return $this->execute("SELECT sf.*, $nameSql FROM eer_shared_files sf 
                              LEFT JOIN employees e ON sf.uploaded_by = e.employee_id 
                              ORDER BY sf.created_at DESC")->fetchAll();
    }

    public function getFileById($id)
    {
        return $this->execute('SELECT * FROM eer_shared_files WHERE eer_shared_file_id = :id', ['id' => $id])->fetch();
    }

    public function deleteFile($id)
    {
        $sql = 'DELETE FROM eer_shared_files WHERE eer_shared_file_id = :id';
        return $this->execute($sql, ['id' => $id]);
    }
}
