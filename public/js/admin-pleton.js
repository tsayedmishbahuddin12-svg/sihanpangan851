// Check if admin is logged in
if (!localStorage.getItem('isAdminLoggedIn')) {
    location.href = 'login.html';
}

const API_URL = 'http://localhost:3000/api';
let editingId = null;

async function loadPletons() {
    try {
        const response = await fetch(`${API_URL}/pleton`);
        const pletons = await response.json();
        
        const container = document.getElementById('pletonList');
        container.innerHTML = '';
        
        if (pletons.length === 0) {
            container.innerHTML = '<p style="text-align:center;color:#666;">Belum ada data</p>';
            return;
        }
        
        pletons.forEach(pleton => {
            const div = document.createElement('div');
            div.className = 'data-item';
            div.innerHTML = `
                <div style="display:flex;align-items:center;gap:1rem;">
                    ${pleton.image ? `<img src="${API_URL.replace('/api', '')}${pleton.image}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">` : ''}
                    <div>
                        <strong>${pleton.name}</strong><br>
                        <small>${pleton.type} - ${pleton.description || ''}</small>
                    </div>
                </div>
                <div class="data-actions">
                    <button class="edit-btn" onclick="editPleton(${pleton.id})">Edit</button>
                    <button class="delete-btn" onclick="deletePleton(${pleton.id})">Hapus</button>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading pletons:', error);
    }
}

document.getElementById('pletonForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('type', document.getElementById('type').value);
    formData.append('name', document.getElementById('pletonName').value);
    formData.append('description', document.getElementById('pletonDesc').value);
    
    const imageFile = document.getElementById('pletonImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    } else if (editingId) {
        formData.append('existingImage', document.getElementById('existingImage').value);
    }
    
    try {
        const url = editingId ? `${API_URL}/pleton/${editingId}` : `${API_URL}/pleton`;
        const method = editingId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method,
            body: formData
        });
        
        const data = await response.json();
        alert(data.message);
        
        resetForm();
        loadPletons();
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    }
});

async function editPleton(id) {
    try {
        const response = await fetch(`${API_URL}/pleton`);
        const pletons = await response.json();
        const pleton = pletons.find(p => p.id === id);
        
        if (pleton) {
            document.getElementById('type').value = pleton.type;
            document.getElementById('pletonName').value = pleton.name;
            document.getElementById('pletonDesc').value = pleton.description || '';
            document.getElementById('existingImage').value = pleton.image || '';
            
            if (pleton.image) {
                const preview = document.getElementById('imagePreview');
                preview.src = `${API_URL.replace('/api', '')}${pleton.image}`;
                preview.classList.add('show');
            }
            
            editingId = id;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deletePleton(id) {
    if (!confirm('Yakin ingin menghapus pleton ini?')) return;
    
    try {
        const response = await fetch(`${API_URL}/pleton/${id}`, { method: 'DELETE' });
        const data = await response.json();
        alert(data.message);
        loadPletons();
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus data');
    }
}

function resetForm() {
    document.getElementById('pletonForm').reset();
    document.getElementById('imagePreview').classList.remove('show');
    document.getElementById('existingImage').value = '';
    editingId = null;
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.add('show');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

loadPletons();
