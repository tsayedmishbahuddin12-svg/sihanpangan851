const express = require('express');
const multer = require('multer');
const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const cors = require('cors');
const fs = require('fs');

const app = express();
const PORT = 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static('public'));
app.use('/uploads', express.static('uploads'));

// Create uploads directory if not exists
if (!fs.existsSync('uploads')) {
    fs.mkdirSync('uploads');
}

// Database setup
const db = new sqlite3.Database('./sihanpangan.db', (err) => {
    if (err) console.error(err);
    else console.log('Database connected');
});

// Create tables
db.serialize(() => {
    db.run(`CREATE TABLE IF NOT EXISTS pleton (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        type TEXT NOT NULL,
        name TEXT NOT NULL,
        description TEXT,
        image TEXT
    )`);

    db.run(`CREATE TABLE IF NOT EXISTS items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        pleton_id INTEGER,
        name TEXT NOT NULL,
        image TEXT,
        FOREIGN KEY (pleton_id) REFERENCES pleton(id)
    )`);

    db.run(`CREATE TABLE IF NOT EXISTS budidaya_data (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        item_id INTEGER,
        kegiatan TEXT NOT NULL,
        luas TEXT,
        populasi TEXT,
        hasil TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES items(id)
    )`);

    db.run(`CREATE TABLE IF NOT EXISTS admin_users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )`);

    // Insert default admin
    db.run(`INSERT OR IGNORE INTO admin_users (username, password) VALUES ('admin', 'admin123')`);
});

// Multer setup for file upload
const storage = multer.diskStorage({
    destination: (req, file, cb) => cb(null, 'uploads/'),
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, uniqueSuffix + path.extname(file.originalname));
    }
});
const upload = multer({ storage });

// Routes

// Auth
app.post('/api/login', (req, res) => {
    const { username, password } = req.body;
    db.get('SELECT * FROM admin_users WHERE username = ? AND password = ?', 
        [username, password], (err, row) => {
        if (err) return res.status(500).json({ error: err.message });
        if (row) res.json({ success: true, message: 'Login berhasil' });
        else res.status(401).json({ success: false, message: 'Username atau password salah' });
    });
});

// Pleton CRUD
app.get('/api/pleton', (req, res) => {
    const { type } = req.query;
    const query = type ? 'SELECT * FROM pleton WHERE type = ?' : 'SELECT * FROM pleton';
    const params = type ? [type] : [];
    db.all(query, params, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.post('/api/pleton', upload.single('image'), (req, res) => {
    const { type, name, description } = req.body;
    const image = req.file ? `/uploads/${req.file.filename}` : null;
    db.run('INSERT INTO pleton (type, name, description, image) VALUES (?, ?, ?, ?)',
        [type, name, description, image], function(err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ id: this.lastID, message: 'Pleton berhasil ditambahkan' });
    });
});

app.put('/api/pleton/:id', upload.single('image'), (req, res) => {
    const { type, name, description } = req.body;
    const image = req.file ? `/uploads/${req.file.filename}` : req.body.existingImage;
    db.run('UPDATE pleton SET type = ?, name = ?, description = ?, image = ? WHERE id = ?',
        [type, name, description, image, req.params.id], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Pleton berhasil diupdate' });
    });
});

app.delete('/api/pleton/:id', (req, res) => {
    db.run('DELETE FROM pleton WHERE id = ?', [req.params.id], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Pleton berhasil dihapus' });
    });
});

// Items CRUD
app.get('/api/items', (req, res) => {
    const { pleton_id } = req.query;
    const query = pleton_id ? 'SELECT * FROM items WHERE pleton_id = ?' : 'SELECT * FROM items';
    const params = pleton_id ? [pleton_id] : [];
    db.all(query, params, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.post('/api/items', upload.single('image'), (req, res) => {
    const { pleton_id, name } = req.body;
    const image = req.file ? `/uploads/${req.file.filename}` : null;
    db.run('INSERT INTO items (pleton_id, name, image) VALUES (?, ?, ?)',
        [pleton_id, name, image], function(err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ id: this.lastID, message: 'Item berhasil ditambahkan' });
    });
});

app.put('/api/items/:id', upload.single('image'), (req, res) => {
    const { pleton_id, name } = req.body;
    const image = req.file ? `/uploads/${req.file.filename}` : req.body.existingImage;
    db.run('UPDATE items SET pleton_id = ?, name = ?, image = ? WHERE id = ?',
        [pleton_id, name, image, req.params.id], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Item berhasil diupdate' });
    });
});

app.delete('/api/items/:id', (req, res) => {
    db.run('DELETE FROM items WHERE id = ?', [req.params.id], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Item berhasil dihapus' });
    });
});

// Budidaya Data CRUD
app.get('/api/budidaya', (req, res) => {
    const { item_id } = req.query;
    const query = item_id ? 'SELECT * FROM budidaya_data WHERE item_id = ?' : 'SELECT * FROM budidaya_data';
    const params = item_id ? [item_id] : [];
    db.all(query, params, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

app.post('/api/budidaya', (req, res) => {
    const { item_id, kegiatan, luas, populasi, hasil } = req.body;
    db.run('INSERT INTO budidaya_data (item_id, kegiatan, luas, populasi, hasil) VALUES (?, ?, ?, ?, ?)',
        [item_id, kegiatan, luas, populasi, hasil], function(err) {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ id: this.lastID, message: 'Data berhasil ditambahkan' });
    });
});

app.put('/api/budidaya/:id', (req, res) => {
    const { item_id, kegiatan, luas, populasi, hasil } = req.body;
    db.run('UPDATE budidaya_data SET item_id = ?, kegiatan = ?, luas = ?, populasi = ?, hasil = ? WHERE id = ?',
        [item_id, kegiatan, luas, populasi, hasil, req.params.id], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Data berhasil diupdate' });
    });
});

app.delete('/api/budidaya/:id', (req, res) => {
    db.run('DELETE FROM budidaya_data WHERE id = ?', [req.params.id], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Data berhasil dihapus' });
    });
});

app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});
