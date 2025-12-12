<h1>Profil de l'utilisateur : <span id="profil_identifiant_titre"><?php echo $user['nom']; ?></span></h1>
<div class="row">
    <div class="col">
        <img class="w-75 rounded mx-auto img-fluid" src="../upload/profil.png" alt="<?php echo $user['nom']; ?>" class="card-img-top">
    </div>
    <div class="col">
        <p><b>Nom :</b> <span id="profil_identifiant" data-id="<?php echo $user['id']; ?>" contenteditable="true"><?php echo $user['nom']; ?></span></p>
        <p><b>Email :</b> <span id="profil_mail" data-id="<?php echo $user['id']; ?>" contenteditable="true"><?php echo $user['email']; ?></span></p>
    </div>
</div>
<hr>
<div id="boutons">
    <button id="bouton_modifier_profil" class="btn btn-primary d-none">Modifier le profil</button>
    <a href="?c=home" class="btn btn-primary">Retour à l'accueil</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spanNom = document.getElementById('profil_identifiant');
        const spanEmail = document.getElementById('profil_mail');
        const boutonModifier = document.getElementById('bouton_modifier_profil');
        const titreNom = document.getElementById('profil_identifiant_titre');
        const headerNom = document.querySelector('.navbar-nav a[href="?c=User&a=profil"]');

        const initialNom = spanNom.textContent.trim();
        const initialEmail = spanEmail.textContent.trim();

        function checkChanges() {
            const isModified = (spanNom.textContent.trim() !== initialNom || spanEmail.textContent.trim() !== initialEmail);
            boutonModifier.classList.toggle('d-none', !isModified);
        }

        spanNom.addEventListener('input', checkChanges);
        spanEmail.addEventListener('input', checkChanges);

        boutonModifier.addEventListener('click', function() {
            const id = spanNom.getAttribute('data-id');
            const newNom = spanNom.textContent.trim();
            const newEmail = spanEmail.textContent.trim();

            if (!newNom || !newEmail) {
                alert("Le nom et l'email ne peuvent pas être vides.");
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('nom', newNom);
            formData.append('email', newEmail);

            fetch('?c=User&a=updateProfilAjax', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau ou réponse non-JSON.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Profil mis à jour avec succès.');
                    
                    titreNom.textContent = newNom;
                    if (headerNom) {
                         headerNom.textContent = 'Profil de ' + newNom;
                    }
                    
                    boutonModifier.classList.add('d-none');

                } else {
                    alert('Erreur: ' + (data.message || 'Échec de la mise à jour du profil.'));
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                alert('Une erreur s\'est produite lors de la communication avec le serveur.');
            });
        });
    });
</script>