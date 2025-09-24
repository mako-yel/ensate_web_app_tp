<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Étudiants - ENSA Tétouan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .tab-button {
            flex: 1;
            padding: 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            background: #e9ecef;
        }

        .tab-button.active {
            background: #007bff;
            color: white;
        }

        .tab-content {
            display: none;
            padding: 30px;
        }

        .tab-content.active {
            display: block;
        }

        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.4em;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #34495e;
        }

        input, select, textarea {
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        button {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .student-card {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .student-name {
            font-size: 1.3em;
            font-weight: bold;
            color: #2c3e50;
        }

        .student-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .info-label {
            font-weight: 600;
            color: #7f8c8d;
        }

        .info-value {
            color: #2c3e50;
        }

        .search-box {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 2px solid #3498db;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }

        .company-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #ecf0f1;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .student-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 ENSA Tétouan</h1>
            <p>Système de Gestion des Étudiants et Stages</p>
        </div>

        <div class="tabs">
            <button class="tab-button active" onclick="showTab('database')">📊 Base de Données</button>
            <button class="tab-button" onclick="showTab('company')">🏢 Gestion des Stages</button>
        </div>

        <!-- DATABASE TAB -->
        <div id="database" class="tab-content active">
            <div class="form-section">
                <h3>🔍 Recherche d'Étudiant</h3>
                <input type="email" id="searchEmail" class="search-box" placeholder="Rechercher par email..." onkeyup="searchStudent()">
                <div id="searchResult"></div>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" id="totalStudents">0</div>
                    <div class="stat-label">Étudiants Total</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(45deg, #27ae60, #229954)">
                    <div class="stat-number" id="totalFilieres">0</div>
                    <div class="stat-label">Filières</div>
                </div>
            </div>

            <div class="form-section">
                <h3>👥 Liste des Étudiants</h3>
                <div id="studentsList"></div>
            </div>
        </div>

        <!-- COMPANY TAB -->
        <div id="company" class="tab-content">
            <div class="form-section">
                <h3>🏢 Ajouter une Entreprise</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="companyName">Nom de l'entreprise *</label>
                        <input type="text" id="companyName" required>
                    </div>
                    <div class="form-group">
                        <label for="companySector">Secteur d'activité *</label>
                        <input type="text" id="companySector" required>
                    </div>
                    <div class="form-group">
                        <label for="companyCity">Ville *</label>
                        <input type="text" id="companyCity" required>
                    </div>
                    <div class="form-group">
                        <label for="companyEmail">Email contact</label>
                        <input type="email" id="companyEmail">
                    </div>
                </div>
                <button onclick="addCompany()">Ajouter Entreprise</button>
            </div>

            <div class="company-section">
                <h3>🎯 Affecter un Stage</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="internEmail">Email de l'étudiant *</label>
                        <input type="email" id="internEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="internCompany">Entreprise *</label>
                        <select id="internCompany" required>
                            <option value="">Sélectionner une entreprise</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="internPosition">Poste de stage</label>
                        <input type="text" id="internPosition">
                    </div>
                    <div class="form-group">
                        <label for="internDuration">Durée (mois)</label>
                        <input type="number" id="internDuration" min="1" max="12">
                    </div>
                </div>
                <button onclick="assignInternship()">Affecter le Stage</button>
            </div>

            <div class="form-section">
                <h3>📋 Gestion des Candidatures</h3>
                <div id="companiesList"></div>
                <div id="internshipsList" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>

    <script>
        // Global arrays for data storage
        let studentsData = [];
        let companiesData = [];
        let internshipsData = [];

        // Load sample data on page load
        window.onload = function() {
            loadSampleData();
            displayStudents();
            displayCompanies();
            updateStats();
            updateCompanySelect();
        };

        function loadSampleData() {
            studentsData = [
                {
                    nom: "Alami",
                    prenom: "Ahmed",
                    age: 22,
                    telephone: "0661234567",
                    email: "ahmed.alami@etu.ensa.ac.ma",
                    filiere: "Génie Informatique",
                    annee: "2ème année",
                    modules: "Java, PHP, MySQL",
                    projets_realises: "Site web e-commerce",
                    projets_stages: "Application mobile",
                    centres_interet: "Intelligence Artificielle, Développement Web",
                    langues: "Arabe, Français, Anglais",
                    remarques: "Étudiant motivé",
                    photo: ""
                },
                {
                    nom: "Benjelloun",
                    prenom: "Fatima",
                    age: 21,
                    telephone: "0677890123",
                    email: "fatima.benjelloun@etu.ensa.ac.ma",
                    filiere: "Génie Civil",
                    annee: "3ème année",
                    modules: "AutoCAD, Structure, Béton armé",
                    projets_realises: "Conception d'un pont",
                    projets_stages: "Bureau d'études",
                    centres_interet: "Architecture, BIM",
                    langues: "Arabe, Français, Espagnol",
                    remarques: "Excellente en conception",
                    photo: ""
                }
            ];

            companiesData = [
                {
                    id: 1,
                    name: "TechnoSoft Maroc",
                    sector: "Technologies de l'information",
                    city: "Tétouan",
                    email: "rh@technosoft.ma"
                },
                {
                    id: 2,
                    name: "Build & Co",
                    sector: "Construction",
                    city: "Tanger",
                    email: "stages@buildco.ma"
                }
            ];
        }

        // Tab switching functionality
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }

        // Database functions
        function searchStudent() {
            const email = document.getElementById('searchEmail').value.toLowerCase();
            const resultDiv = document.getElementById('searchResult');
            
            if (email === '') {
                resultDiv.innerHTML = '';
                return;
            }
            
            const student = studentsData.find(s => s.email.toLowerCase().includes(email));
            
            if (student) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <strong>Étudiant trouvé:</strong> ${student.prenom} ${student.nom} (${student.email})
                    </div>
                    ${createStudentCard(student)}
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        Aucun étudiant trouvé avec cet email.
                    </div>
                `;
            }
        }

        function createStudentCard(student) {
            return `
                <div class="student-card">
                    <div class="student-header">
                        <div class="student-name">${student.prenom} ${student.nom}</div>
                        <div style="color: #3498db; font-weight: 600;">${student.filiere}</div>
                    </div>
                    <div class="student-info">
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">${student.email}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Âge:</span>
                            <span class="info-value">${student.age} ans</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Téléphone:</span>
                            <span class="info-value">${student.telephone}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Année:</span>
                            <span class="info-value">${student.annee}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Modules:</span>
                            <span class="info-value">${student.modules}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Projets:</span>
                            <span class="info-value">${student.projets_realises}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Langues:</span>
                            <span class="info-value">${student.langues}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Centres d'intérêt:</span>
                            <span class="info-value">${student.centres_interet}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        function displayStudents() {
            const listDiv = document.getElementById('studentsList');
            listDiv.innerHTML = studentsData.map(student => createStudentCard(student)).join('');
        }

        function updateStats() {
            document.getElementById('totalStudents').textContent = studentsData.length;
            const uniqueFilieres = [...new Set(studentsData.map(s => s.filiere))];
            document.getElementById('totalFilieres').textContent = uniqueFilieres.length;
        }

        // Company management functions
        function addCompany() {
            const name = document.getElementById('companyName').value.trim();
            const sector = document.getElementById('companySector').value.trim();
            const city = document.getElementById('companyCity').value.trim();
            const email = document.getElementById('companyEmail').value.trim();
            
            if (!name || !sector || !city) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            const newCompany = {
                id: Date.now(),
                name: name,
                sector: sector,
                city: city,
                email: email
            };
            
            companiesData.push(newCompany);
            
            // Clear form
            document.getElementById('companyName').value = '';
            document.getElementById('companySector').value = '';
            document.getElementById('companyCity').value = '';
            document.getElementById('companyEmail').value = '';
            
            displayCompanies();
            updateCompanySelect();
            alert('Entreprise ajoutée avec succès!');
        }

        function displayCompanies() {
            const listDiv = document.getElementById('companiesList');
            listDiv.innerHTML = `
                <h4>🏢 Entreprises Partenaires (${companiesData.length})</h4>
                ${companiesData.map(company => `
                    <div class="student-card">
                        <div class="student-header">
                            <div class="student-name">${company.name}</div>
                            <div style="color: #e74c3c; font-weight: 600;">${company.sector}</div>
                        </div>
                        <div class="student-info">
                            <div class="info-item">
                                <span class="info-label">Ville:</span>
                                <span class="info-value">${company.city}</span>
                            </div>
                            ${company.email ? `
                                <div class="info-item">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value">${company.email}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `).join('')}
            `;
        }

        function updateCompanySelect() {
            const select = document.getElementById('internCompany');
            select.innerHTML = '<option value="">Sélectionner une entreprise</option>';
            companiesData.forEach(company => {
                select.innerHTML += `<option value="${company.id}">${company.name} (${company.city})</option>`;
            });
        }

        function assignInternship() {
            const email = document.getElementById('internEmail').value.trim();
            const companyId = document.getElementById('internCompany').value;
            const position = document.getElementById('internPosition').value.trim();
            const duration = document.getElementById('internDuration').value;
            
            if (!email || !companyId) {
                alert('Veuillez remplir les champs obligatoires.');
                return;
            }
            
            const student = studentsData.find(s => s.email.toLowerCase() === email.toLowerCase());
            if (!student) {
                alert('Étudiant non trouvé avec cet email.');
                return;
            }
            
            const company = companiesData.find(c => c.id == companyId);
            
            const internship = {
                id: Date.now(),
                studentEmail: email,
                studentName: `${student.prenom} ${student.nom}`,
                companyId: companyId,
                companyName: company.name,
                position: position || 'Stagiaire',
                duration: duration || 'Non spécifié',
                date: new Date().toLocaleDateString('fr-FR')
            };
            
            internshipsData.push(internship);
            
            // Clear form
            document.getElementById('internEmail').value = '';
            document.getElementById('internCompany').value = '';
            document.getElementById('internPosition').value = '';
            document.getElementById('internDuration').value = '';
            
            displayInternships();
            alert('Stage affecté avec succès!');
        }

        function displayInternships() {
            const listDiv = document.getElementById('internshipsList');
            if (internshipsData.length === 0) {
                listDiv.innerHTML = '<p style="text-align: center; color: #7f8c8d;">Aucun stage affecté pour le moment.</p>';
                return;
            }
            
            listDiv.innerHTML = `
                <h4>🎯 Stages Affectés (${internshipsData.length})</h4>
                ${internshipsData.map(internship => `
                    <div class="student-card">
                        <div class="student-header">
                            <div class="student-name">${internship.studentName}</div>
                            <div style="color: #27ae60; font-weight: 600;">${internship.companyName}</div>
                        </div>
                        <div class="student-info">
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value">${internship.studentEmail}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Poste:</span>
                                <span class="info-value">${internship.position}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Durée:</span>
                                <span class="info-value">${internship.duration} mois</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date d'affectation:</span>
                                <span class="info-value">${internship.date}</span>
                            </div>
                        </div>
                    </div>
                `).join('')}
            `;
        }

        // Initialize internships display
        displayInternships();
    </script>
</body>
</html>