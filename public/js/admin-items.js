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
        
        const select = document.getElementById('pletonSelect');
        select.innerHTML = '<option value="">Pilih Pleton</option>';
        
        pletons.forEach(pleton => {
            const option = document.createElement('option');
            option.value = pleton.id;
            option.textContent = `${pleton.name} (${pleton.type})`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading pletons:', error);
    }
}

async function loadItems() {
    try {
        const response = await fetch(`${API_URL}/items`);
        const items = await response.json();
        
        const container = document.getElementById('itemList');
        container.innerHTML = '';
        
        if (items.length === 0) {
            container.innerHTML = '<p style="text-align:center;color:#666;">Belum ada data</p>';
            return;
        }
        
        for (const item of items) {
            const div = document.createElement('div');
            div.className = 'data-item';
            div.innerHTML = `
                <div style="display:flex;align-items:center;gap:1rem;">
                    ${item.image ? `<img src="${API_URL.replace('/api', '')}${item.image}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">` : ''}
                    <div>
                        <strong>${item.name}</strong><br>
                        <small>Pleton ID: ${item.pleton_id}</small>
                    </div>
                </div>
                <div class="data-actions">
                    <button class="edit-btn" onclick="editItem(${item.id})">Edit</button>
                    <button class="delete-btn" onclick="deleteItem(${item.id})">Hapus</button>
                </div>
            `;
            container.appendChild(div);
        }
    } catch (error) {
        console.error('Error loading items:', error);
    }
}

document.getElementById('itemForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('pleton_id', document.getElementById('pletonSelect').value);
    formData.append('name', document.getElementById('itemName').value);
    
    const imageFile = document.getElementById('itemImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    } else if (editingId) {
        formData.append('existingImage', document.getElementById('existingImage').value);
    }
    
    try {
        const url = editingId ? `${API_URL}/items/${editingId}` : `${API_URL}/items`;
        const method = editingId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method,
            body: formData
        });
        
        const data = await response.json();
        alert(data.message);
        
        resetForm();
        loadItems();
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    }
});

async function editItem(id) {
    try {
        const response = await fetch(`${API_URL}/items`);
        const items = await response.json();
        const item = items.find(i => i.id === id);
        
        if (item) {
            document.getElementById('pletonSelect').value = item.pleton_id;
            document.getElementById('itemName').value = item.name;
            document.getElementById('existingImage').value = item.image || '';
            
            if (item.image) {
                const preview = document.getElementById('imagePreview');
                preview.src = `${API_URL.replace('/api', '')}${item.image}`;
                preview.classList.add('show');
            }
            
            editingId = id;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteItem(id) {
    if (!confirm('Yakin ingin menghapus item ini?')) return;
    
    try {
        const response = await fetch(`${API_URL}/items/${id}`, { method: 'DELETE' });
        const data = await response.json();
        alert(data.message);
        loadItems();
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus data');
    }
}

function resetForm() {
    document.getElementById('itemForm').reset();
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
loadItems();
