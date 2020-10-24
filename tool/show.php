<div>
    <div id = "array" style="display:block; width: 49.5%">
        <?php
        $arrResult= json_decode($_POST['str'],true);
        echo "<pre>";
        echo var_export($arrResult);
        echo "</pre>";
        ?>
    </div>
</div>
