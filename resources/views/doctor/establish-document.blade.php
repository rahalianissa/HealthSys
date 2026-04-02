@extends('layouts.app')

@section('title', 'Établir un document')
@section('page-title', 'Établir documents pour patient')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sélectionner un patient</h5>
            </div>
            <div class="card-body">
                <input type="text" id="searchPatient" class="form-control" placeholder="Nom du patient">
                <div id="patientList" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-4">
        <div id="documentForm" style="display: none;">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Établir un document</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#ordonnance">Ordonnance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#certificat">Certificat médical</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#rapport">Compte rendu</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Ordonnance -->
                        <div class="tab-pane fade show active" id="ordonnance">
                            <form id="prescriptionForm">
                                <div id="medicationsContainer">
                                    <div class="medication-item row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" name="medicament[]" class="form-control" placeholder="Médicament" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="dosage[]" class="form-control" placeholder="Dosage" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="duree[]" class="form-control" placeholder="Durée" required>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger" onclick="this.closest('.medication-item').remove()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addMedication()">
                                    <i class="fas fa-plus"></i> Ajouter médicament
                                </button>
                                <div class="mb-3">
                                    <label>Instructions</label>
                                    <textarea id="instructions" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="button" class="btn btn-custom" onclick="savePrescription()">Générer ordonnance</button>
                            </form>
                        </div>
                        
                        <!-- Certificat médical -->
                        <div class="tab-pane fade" id="certificat">
                            <form id="certificateForm">
                                <div class="mb-3">
                                    <label>Type de certificat</label>
                                    <select id="certificate_type" class="form-control">
                                        <option value="repos">Certificat de repos</option>
                                        <option value="aptitude">Certificat d'aptitude</option>
                                        <option value="vaccination">Certificat de vaccination</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Durée (jours)</label>
                                    <input type="number" id="certificate_duration" class="form-control" value="3">
                                </div>
                                <div class="mb-3">
                                    <label>Motif</label>
                                    <textarea id="certificate_reason" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="button" class="btn btn-custom" onclick="saveCertificate()">Générer certificat</button>
                            </form>
                        </div>
                        
                        <!-- Compte rendu -->
                        <div class="tab-pane fade" id="rapport">
                            <form id="reportForm">
                                <div class="mb-3">
                                    <label>Diagnostic</label>
                                    <textarea id="report_diagnosis" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Traitement proposé</label>
                                    <textarea id="report_treatment" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Recommandations</label>
                                    <textarea id="report_recommendations" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="button" class="btn btn-custom" onclick="saveReport()">Générer compte rendu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPatientId = null;

document.getElementById('searchPatient').addEventListener('keyup', function() {
    const search = this.value;
    if(search.length < 2) return;
    
    fetch(`/patients/search?q=${search}`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="list-group">';
            data.forEach(patient => {
                html += `<a href="#" class="list-group-item list-group-item-action" onclick="selectPatient(${patient.id})">${patient.user.name}</a>`;
            });
            html += '</div>';
            document.getElementById('patientList').innerHTML = html;
        });
});

function selectPatient(id) {
    selectedPatientId = id;
    document.getElementById('documentForm').style.display = 'block';
    document.getElementById('patientList').innerHTML = '';
    document.getElementById('searchPatient').disabled = true;
}

function addMedication() {
    const container = document.getElementById('medicationsContainer');
    const count = container.children.length;
    const div = document.createElement('div');
    div.className = 'medication-item row mb-2';
    div.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="medicament[]" class="form-control" placeholder="Médicament" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="dosage[]" class="form-control" placeholder="Dosage" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="duree[]" class="form-control" placeholder="Durée" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger" onclick="this.closest('.medication-item').remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}

function savePrescription() {
    const medicaments = [];
    const items = document.querySelectorAll('.medication-item');
    items.forEach(item => {
        medicaments.push({
            name: item.querySelector('[name="medicament[]"]').value,
            dosage: item.querySelector('[name="dosage[]"]').value,
            duration: item.querySelector('[name="duree[]"]').value
        });
    });
    
    fetch('{{ route("doctor.store-prescription") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            patient_id: selectedPatientId,
            medications: medicaments,
            instructions: document.getElementById('instructions').value
        })
    }).then(response => response.json()).then(data => {
        if(data.success) {
            window.open(data.pdf_url, '_blank');
            alert('Ordonnance générée avec succès');
        }
    });
}

function saveCertificate() {
    fetch('{{ route("doctor.store-certificate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            patient_id: selectedPatientId,
            type: document.getElementById('certificate_type').value,
            duration: document.getElementById('certificate_duration').value,
            reason: document.getElementById('certificate_reason').value
        })
    }).then(response => response.json()).then(data => {
        if(data.success) {
            window.open(data.pdf_url, '_blank');
            alert('Certificat généré avec succès');
        }
    });
}

function saveReport() {
    fetch('{{ route("doctor.store-report") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            patient_id: selectedPatientId,
            diagnosis: document.getElementById('report_diagnosis').value,
            treatment: document.getElementById('report_treatment').value,
            recommendations: document.getElementById('report_recommendations').value
        })
    }).then(response => response.json()).then(data => {
        if(data.success) {
            window.open(data.pdf_url, '_blank');
            alert('Compte rendu généré avec succès');
        }
    });
}
</script>
@endsection