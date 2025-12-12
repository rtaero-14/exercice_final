<h1>Modifier le Post : <?php echo htmlspecialchars($post['titre']); ?></h1>

<form action="?c=Post&a=update" method="post">
    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
    
    <div class="mb-3">
        <label for="titre" class="form-label">Titre</label>
        <input type="text" class="form-control" name="titre" id="titre" value="<?php echo htmlspecialchars($post['titre']); ?>" required>
    </div>

    <div class="mb-3">
        <label for="contenu" class="form-label">Contenu du message</label>
        <textarea class="form-control" name="contenu" id="contenu" rows="5" required><?php echo htmlspecialchars($post['contenu']); ?></textarea>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="?c=Post&a=lister" class="btn btn-secondary">Annuler</a>
    </div>
</form>