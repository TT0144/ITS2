document.getElementById('post-spot-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const photo = document.getElementById('photo').files[0];
    const region = document.getElementById('region').value;

    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('photo', photo);
    formData.append('region', region);

    const response = await fetch('/api/post-spot', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    if (result.success) {
        window.location.href = 'dashboard.html';
    } else {
        alert('Post failed: ' + result.message);
    }
});

