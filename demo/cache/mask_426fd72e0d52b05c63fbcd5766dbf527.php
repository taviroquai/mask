<!DOCTYPE html>
<html>
    <head>
        <title><?=$title?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        
        <h1><?=$title?></h1>
        
        <?php if (!empty($this->hasLogic())) : ?>
            <p>Description: <?=$description?></p>
        <?php endif; ?>
        
        <ul>
        <?php foreach($items as $item) { ?>
        <li><?=$item?></li>
        <?php } ?>
        </ul>
        
    </body>
</html>
