<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
$posters = db()->query('SELECT id,name,image_path FROM ar_posters ORDER BY name')->fetchAll();
$posterId = (int)($_GET['poster_id'] ?? $_POST['poster_id'] ?? ($posters[0]['id'] ?? 0));
$attractions = db()->query("SELECT id,name FROM attractions WHERE status='active' ORDER BY name")->fetchAll();
if (is_post()) {
    verify_csrf();
    try {
        if (($_POST['action'] ?? '') === 'save') {
            $id=(int)($_POST['id']??0);
            $label=trim($_POST['label']??'');
            $attraction=(int)($_POST['attraction_id']??0);
            $x=max(0, min(1, (float)($_POST['x']??.1)));
            $y=max(0, min(1, (float)($_POST['y']??.1)));
            $w=max(.02, min(1, (float)($_POST['width']??.2)));
            $h=max(.02, min(1, (float)($_POST['height']??.15)));
            if (!$label) {
                throw new RuntimeException('Hotspot label is required.');
            }
            if ($id) {
                $s=db()->prepare('UPDATE ar_hotspots SET attraction_id=?,label=?,x=?,y=?,width=?,height=?,content_type=?,video_url=? WHERE id=? AND poster_id=?');
                $s->execute([$attraction ?: null,$label,$x,$y,$w,$h,$_POST['content_type']==='video' ? 'video' : 'info',trim($_POST['video_url']??''),$id,$posterId]);
            } else {
                $s=db()->prepare('INSERT INTO ar_hotspots(poster_id,attraction_id,label,x,y,width,height,content_type,video_url) VALUES(?,?,?,?,?,?,?,?,?)');
                $s->execute([$posterId,$attraction ?: null,$label,$x,$y,$w,$h,$_POST['content_type']==='video' ? 'video' : 'info',trim($_POST['video_url']??'')]);
            }
            log_action('update', 'Saved AR hotspot '.$label);
            flash('success', 'Hotspot saved.');
        } elseif (($_POST['action']??'')==='delete') {
            db()->prepare('DELETE FROM ar_hotspots WHERE id=? AND poster_id=?')->execute([(int)$_POST['id'],$posterId]);
            flash('success', 'Hotspot deleted.');
        }
    } catch (Throwable $error) {
        flash('danger', $error->getMessage());
    }
    redirect('admin/hotspots.php?poster_id='.$posterId);
}
$poster=null; foreach ($posters as $item) {
    if ((int)$item['id']===$posterId) {
        $poster=$item;
    }
}
$hotspots=[]; if ($poster) {
    $s=db()->prepare('SELECT h.*,a.name attraction_name FROM ar_hotspots h LEFT JOIN attractions a ON a.id=h.attraction_id WHERE h.poster_id=? ORDER BY h.id');
    $s->execute([$posterId]);
    $hotspots=$s->fetchAll();
}
$pageTitle='AR Hotspots';require __DIR__.'/_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><div class="eyebrow text-success">AR MANAGEMENT</div><h1 class="admin-title mb-1">Hotspot editor</h1><p class="text-muted mb-0">Place interactive information on your tourism poster.</p></div><form><select name="poster_id" class="form-select" onchange="this.form.submit()"><?php foreach ($posters as $item):?><option value="<?=$item['id']?>" <?=$posterId===$item['id'] ? 'selected' : ''?>><?=e($item['name'])?></option><?php endforeach;?></select></form></div>
<?php if (!$poster):?><div class="empty-state"><i class="fa-solid fa-image fa-2x mb-3"></i><h3>No posters available</h3><p>Upload an AR poster first.</p><a class="btn btn-success" href="<?=url('admin/posters.php')?>">Upload poster</a></div><?php else:?>
<div class="row g-4"><div class="col-xl-8"><div class="card p-3"><div class="poster-editor admin-poster-editor"><img src="<?=asset($poster['image_path'])?>" alt="<?=e($poster['name'])?>"><?php foreach ($hotspots as $h):?><span class="hotspot" style="left:<?=$h['x']*100?>%;top:<?=$h['y']*100?>%;width:<?=$h['width']*100?>%;height:<?=$h['height']*100?>%"><?=e($h['label'])?></span><?php endforeach;?></div><small class="text-muted mt-3 d-block">Coordinates are stored as normalized percentages so the layout works on every screen size.</small></div></div><div class="col-xl-4"><div class="card p-4"><h5 class="mb-3">Add hotspot</h5><form method="post"><?=csrf_field()?><input type="hidden" name="action" value="save"><input type="hidden" name="poster_id" value="<?=$posterId?>"><div class="mb-3"><label class="form-label">Label</label><input name="label" class="form-control" placeholder="Museum entrance" required></div><div class="mb-3"><label class="form-label">Attraction</label><select name="attraction_id" class="form-select"><option value="0">Choose attraction</option><?php foreach ($attractions as $a):?><option value="<?=$a['id']?>"><?=e($a['name'])?></option><?php endforeach;?></select></div><div class="row g-2 mb-3"><?php foreach (['x'=>'X','y'=>'Y','width'=>'Width','height'=>'Height'] as $key=>$label):?><div class="col-6"><label class="form-label small"><?=$label?> (0–1)</label><input name="<?=$key?>" type="number" step="0.01" min="0" max="1" value="<?=$key==='width' ? .2 : ($key==='height' ? .15 : .1)?>" class="form-control"></div><?php endforeach;?></div><select name="content_type" class="form-select mb-3"><option value="info">Information card</option><option value="video">Video overlay</option></select><input name="video_url" class="form-control mb-3" placeholder="Optional YouTube URL"><button class="btn btn-success w-100">Save hotspot</button></form></div></div></div><div class="card p-4 mt-4"><h5>Configured hotspots</h5><div class="table-responsive"><table class="table align-middle mb-0"><tr><th>Label</th><th>Attraction</th><th>Position</th><th></th></tr><?php foreach ($hotspots as $h):?><tr><td><?=e($h['label'])?></td><td><?=e($h['attraction_name']??'Unassigned')?></td><td class="text-muted small"><?=number_format($h['x'], 2)?>, <?=number_format($h['y'], 2)?></td><td><form method="post" class="d-inline"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="poster_id" value="<?=$posterId?>"><input type="hidden" name="id" value="<?=$h['id']?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Delete this hotspot?">Delete</button></form></td></tr><?php endforeach;?></table></div></div>
<?php endif; require __DIR__.'/_footer.php'; ?>
