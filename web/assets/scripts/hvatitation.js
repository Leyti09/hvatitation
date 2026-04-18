function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    
    if (file) {
      const reader = new FileReader();
      
      reader.onload = function() {
        preview.src = reader.result;
      };
      
      reader.readAsDataURL(file);
    }
  }
