<?php
function parent($data, $parent = 0, $str="--",$select=0){
        foreach ($data as $val ){
            $id = $val["id"];
            $name = $val["name"];
            if ($val["parent_id"] == $parent){
                if ($select !=0 && $id == $select) {
                    echo "<option value='$id' selected='selected'>$str $name </option>";
                }else{
                    echo "<option value='$id'> $str $name</option>";
                }
                parent ($data,$id,$str."--");
            }
        }
    }
