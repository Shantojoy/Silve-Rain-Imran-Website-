<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id=(int)($_GET['id'] ?? 0);
$edit=null;
if($id){$s=$pdo->prepare('SELECT * FROM blogs WHERE id=?');$s->execute([$id]);$edit=$s->fetch();}

if($_SERVER['REQUEST_METHOD']==='POST'){
    try{
        $id=(int)($_POST['id'] ?? 0);
        $title=trim($_POST['title'] ?? '');
        $content=$_POST['content'] ?? '';
        $categoryId=!empty($_POST['category_id'])?(int)$_POST['category_id']:null;
        $metaTitle=trim($_POST['meta_title'] ?? '');
        $metaDescription=trim($_POST['meta_description'] ?? '');
        if($title==='' || trim(strip_tags($content))==='') throw new RuntimeException('Title and content are required.');

        $slugBase=slugify($title);$slug=$slugBase;$i=1;
        while(true){$q=$pdo->prepare('SELECT id FROM blogs WHERE slug=?'.($id?' AND id!=?':'').' LIMIT 1');$q->execute($id?[$slug,$id]:[$slug]);if(!$q->fetch())break;$slug=$slugBase.'-'.$i++;}

        $image=uploadImage('featured_image', __DIR__.'/../uploads/products');
        if($id){
            $cur=$pdo->prepare('SELECT featured_image FROM blogs WHERE id=?');$cur->execute([$id]);$old=$cur->fetch();
            $pdo->prepare('UPDATE blogs SET title=?,slug=?,content=?,featured_image=?,category_id=?,meta_title=?,meta_description=? WHERE id=?')->execute([$title,$slug,$content,$image?:($old['featured_image']??null),$categoryId,$metaTitle,$metaDescription,$id]);
            setFlash('success','Blog updated.');
        } else {
            $pdo->prepare('INSERT INTO blogs (title,slug,content,featured_image,category_id,meta_title,meta_description) VALUES (?,?,?,?,?,?,?)')->execute([$title,$slug,$content,$image,$categoryId,$metaTitle,$metaDescription]);
            setFlash('success','Blog created.');
        }
        redirect('blogs.php');
    }catch(Throwable $e){setFlash('danger',$e->getMessage()); redirect('blog-edit.php'.($id?'?id='.$id:''));}
}

$cats=$pdo->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title"><?= $edit ? 'Edit Blog' : 'Add New Blog'; ?></h1>
<div class="card p-3 shadow-sm"><form method="post" enctype="multipart/form-data" class="row g-3"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>"><div class="col-md-8"><label class="form-label">Title <span class="text-danger">*</span></label><input name="title" class="form-control" value="<?= htmlspecialchars($edit['title'] ?? ''); ?>" placeholder="Top 10 Wallpaper Trends" required><?= helpText('Slug is generated automatically from title for SEO URL.'); ?></div><div class="col-md-4"><label class="form-label">Category</label><select name="category_id" class="form-select"><option value="">Select category</option><?php foreach($cats as $c): ?><option value="<?= $c['id']; ?>" <?= (int)($edit['category_id'] ?? 0)===(int)$c['id']?'selected':''; ?>><?= htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Featured Image</label><input type="file" name="featured_image" class="form-control" accept="image/jpeg,image/png"></div><div class="col-md-6"><label class="form-label">Meta Title</label><input name="meta_title" class="form-control" value="<?= htmlspecialchars($edit['meta_title'] ?? ''); ?>" placeholder="SEO title"></div><div class="col-12"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="2" placeholder="Short SEO description"><?= htmlspecialchars($edit['meta_description'] ?? ''); ?></textarea></div><div class="col-12"><label class="form-label">Content <span class="text-danger">*</span></label><textarea name="content" class="form-control editor" rows="12"><?= htmlspecialchars($edit['content'] ?? ''); ?></textarea></div><div class="col-12"><button class="btn btn-dark"><i class="bi bi-save"></i> Save Blog</button> <a class="btn btn-outline-secondary" href="blogs.php">Back</a></div></form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
