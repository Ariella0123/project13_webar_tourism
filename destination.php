<?php require_once __DIR__.'/includes/functions.php'; $slug=$_GET['slug']??''; try {
    $stmt=db()->prepare("SELECT d.*,c.name category_name FROM destinations d LEFT JOIN categories c ON c.id=d.category_id WHERE d.slug=? AND d.status='active'");
    $stmt->execute([$slug]);
    $d=$stmt->fetch();
    $a=db()->prepare("SELECT * FROM attractions WHERE destination_id=? AND status='active' ORDER BY display_order,name");
    $a->execute([$d['id']??0]);
    $items=$a->fetchAll();
} catch (Throwable $error) {
    $fallback=['heritage-district'=>['name'=>'Heritage District','short_description'=>'Stories, architecture and living culture.','description'=>'Explore a walkable district filled with local history, crafts and community stories.'],'nature-escape'=>['name'=>'Nature Escape','short_description'=>'Green trails and open skies.','description'=>'A peaceful destination for families and outdoor explorers.'],'cultural-village'=>['name'=>'Cultural Village','short_description'=>'Crafts, food and local traditions.','description'=>'Meet local makers and discover the traditions that shape this community.']];
    $d=$fallback[$slug]??null;
    $items=[];
} if (!$d) {
    http_response_code(404);
    exit('Destination not found');
} $pageTitle=$d['name'];require __DIR__.'/includes/header.php'; ?>
<section class="container py-5"><span class="text-success"><?=e($d['category_name'])?></span><h1 class="section-title"><?=e($d['name'])?></h1><p class="lead"><?=e($d['description'])?></p><h2 class="section-title mt-5 mb-4">Attractions</h2><div class="row g-4"><?php foreach ($items as $item):?><div class="col-md-4"><div class="card h-100"><img class="card-img-top" src="<?=$item['main_image'] ? asset($item['main_image']) : asset('images/hero.svg')?>" alt="<?=e($item['name'])?>"><div class="card-body"><h5><?=e($item['name'])?></h5><p><?=e($item['short_description'])?></p><a href="<?=url('attraction.php?slug='.urlencode($item['slug']))?>" class="btn btn-success">Details</a></div></div></div><?php endforeach;?></div></section><?php require __DIR__.'/includes/footer.php'; ?>
