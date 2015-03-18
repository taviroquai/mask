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
            <?=$this->getSubView()?>
        <?php endif; ?>
        
        <ul>
        <?php foreach($items as $item) { ?>
        <li><?=$item?></li>
        <?php } ?>
        </ul>
        
        <p>Description: <?=$description?></p>
        
    </body>
</html>
