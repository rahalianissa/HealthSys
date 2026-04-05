@extends('layouts.app')

@section('title', 'Assistant Médical')
@section('page-title', 'Assistant Médical AI')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-robot me-2"></i> Assistant Médical AI
                </h5>
                <small>Décrivez vos symptômes, je vous guiderai</small>
            </div>
            <div class="card-body">
                <div id="chatMessages" style="height: 400px; overflow-y: auto; margin-bottom: 20px; padding: 10px;">
                    <div class="alert alert-secondary">
                        <i class="fas fa-robot me-2"></i>
                        Bonjour ! Je suis votre assistant médical. Décrivez-moi vos symptômes en détail, je vous aiderai à identifier la spécialité appropriée et vous donnerai des conseils.
                    </div>
                </div>
                
                <form id="symptomForm" class="d-flex gap-2">
                    @csrf
                    <textarea id="symptoms" class="form-control" rows="2" 
                        placeholder="Ex: J'ai mal à la poitrine, des palpitations et je me sens fatigué..." 
                        style="resize: none;"></textarea>
                    <button type="submit" class="btn btn-primary" style="border-radius: 10px;">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('symptomForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const symptoms = document.getElementById('symptoms').value.trim();
    if (!symptoms) return;
    
    const chatDiv = document.getElementById('chatMessages');
    
    // Ajouter message utilisateur
    chatDiv.innerHTML += `<div class="alert alert-primary mt-2">
        <i class="fas fa-user me-2"></i> ${escapeHtml(symptoms)}
    </div>`;
    
    document.getElementById('symptoms').value = '';
    chatDiv.scrollTop = chatDiv.scrollHeight;
    
    // Indicateur de chargement
    chatDiv.innerHTML += `<div class="alert alert-secondary" id="loading">
        <i class="fas fa-spinner fa-spin me-2"></i> Analyse en cours...
    </div>`;
    chatDiv.scrollTop = chatDiv.scrollHeight;
    
    try {
        const response = await fetch('{{ route("assistant.analyze") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ symptoms: symptoms })
        });
        
        const data = await response.json();
        document.getElementById('loading').remove();
        
        if (data.success) {
            let urgencyClass = 'info';
            if (data.urgency.level == 'emergency') urgencyClass = 'danger';
            else if (data.urgency.level == 'high') urgencyClass = 'warning';
            else if (data.urgency.level == 'medium') urgencyClass = 'primary';
            
            let responseHtml = `<div class="alert alert-${urgencyClass} mt-2">
                <i class="fas fa-robot me-2"></i>
                <strong>🔍 Analyse :</strong><br>
                <strong>Spécialité recommandée :</strong> ${data.specialty}<br>
                <strong>Niveau d'urgence :</strong> <span class="badge bg-${urgencyClass}">${data.urgency.message}</span><br><br>
                <strong>💡 Conseils :</strong><br>
                ${data.advice.replace(/\n/g, '<br>')}
            </div>`;
            
            if (data.doctor) {
                responseHtml += `<div class="alert alert-info mt-2">
                    <strong>👨‍⚕️ Médecin disponible :</strong> Dr. ${data.doctor.name}<br>
                    <button class="btn btn-sm btn-success mt-2" onclick="window.location.href='/patient/appointments?doctor=${data.doctor.id}'">
                        <i class="fas fa-calendar-plus"></i> Prendre rendez-vous
                    </button>
                </div>`;
            }
            
            chatDiv.innerHTML += responseHtml;
        } else {
            chatDiv.innerHTML += `<div class="alert alert-danger mt-2">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${data.message || 'Désolé, une erreur s\'est produite. Veuillez réessayer.'}
            </div>`;
        }
        
        chatDiv.scrollTop = chatDiv.scrollHeight;
        
    } catch (error) {
        document.getElementById('loading').remove();
        chatDiv.innerHTML += `<div class="alert alert-danger mt-2">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Désolé, une erreur s'est produite. Veuillez réessayer.
        </div>`;
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection