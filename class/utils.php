<?php

class utils {
   
    public static function mysqli_result($result, $row, $field = 0) //used to quickly retrieve the first result in mysqli queries
    {
        // Adjust the result pointer to that specific row
        $result->data_seek($row);
        // Fetch result array
        $data = $result->fetch_array();
        return $data[$field];
    }
}
