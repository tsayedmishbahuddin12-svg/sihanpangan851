// Check if admin is logged in
if (!localStorage.getItem('isAdminLoggedIn')) {
    location.href = 'login.html';
}

const API_URL = 'http://localhost:3000/api';
let editingId = null;

async function loadItems() {
    try {
        const response = await fetch(`${API_URL}/items`);
        const items = await response.json();
        
        const select = document.getElementById('itemSelect');
        select.innerHTML = '<option value="">Pilih Item</option>';
        
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading items:', error);
    }
}

async function loadBudidayaData() {
    try {
        const response = await fetch(`${API_URL}/budidaya`);
        const data = await response.json();
        
        const container = document.getElementById('dataList');
        container.innerHTML = '';
        
        if (data.length === 0) {
            container.innerHTML = '<p style="text-align:center;color:#666;">Belum ada data</p>';
            return;
        }
        
        data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'data-item';
            div.innerHTML = `
                <div>
                    <strong>${item.kegiatan}</strong><br>
                    <small>Luas: ${item.luas} | Populasi: ${item.populasi} | Hasil: ${item.hasil}</small>
                </div>
                <div class="data-actions">
                    <button class="edit-btn" onclick="editData(${item.id})">Edit</button>
                    <button class="delete-btn" onclick="deleteData(${item.id})">Hapus</button>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading data:', error);
    }
}

document.getElementById('dataForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const data = {
        item_id: document.getElementById('itemSelect').value,
        kegiatan: document.getElementById('kegiatan').value,
        luas: document.getElementById('luas').value,
        populasi: document.getElementById('populasi').value,
        hasil: document.getElementById('hasil').value
    };
    
    try {
        const url = editingId ? `${API_URL}/budidaya/${editingId}` : `${API_URL}/budidaya`;
        const method = editingId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        alert(result.message);
        
        resetForm();
        loadBudidayaData();
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    }
});

async function editData(id) {
    try {
        const response = await fetch(`${API_URL}/budidaya`);
        const dataList = await response.json();
        const data = dataList.find(d => d.id === id);
        
        if (data) {
            document.getElementById('itemSelect').value = data.item_id;
            document.getElementById('kegiatan').value = data.kegiatan;
            document.getElementById('luas').value = data.luas;
            document.getElementById('populasi').value = data.populasi;
            document.getElementById('hasil').value = data.hasil;
            editingId = id;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteData(id) {
    if (!confirm('Yakin ingin menghapus data ini?')) return;
    
    try {
        const response = await fetch(`${API_URL}/budidaya/${id}`, { method: 'DELETE' });
        const data = await response.json();
        alert(data.message);
        loadBudidayaData();
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus data');
    }
}

function resetForm() {
    document.getElementById('dataForm').reset();
    editingId = null;
}

loadItems();
loadBudidayaData();
