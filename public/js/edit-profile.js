
        function previewPhoto(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]); 
            }
        }

        
        document.getElementById('editProfileForm').addEventListener('submit', function(event) {
            event.preventDefault(); 
            alert('Profil berhasil diperbarui! (Ini adalah contoh, data tidak benar-benar dikirim)');
        });