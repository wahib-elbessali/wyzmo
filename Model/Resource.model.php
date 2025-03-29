<?php

class Resource extends Model
{
    public function __construct()
    {
        parent::__construct();
        $sql = "CREATE TABLE Resources (
          resource_id INT PRIMARY KEY AUTO_INCREMENT,
          project_id INT NOT NULL,
          uploaded_by INT NOT NULL,
          file_name VARCHAR(255) NOT NULL,
          file_path VARCHAR(255) NOT NULL,
          uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (project_id) REFERENCES Projects(project_id),
          FOREIGN KEY (uploaded_by) REFERENCES Users(user_id)
      );";
        $this->db->query($sql);
    }
}
