import './bootstrap';

document.addEventListener('livewire:init', () => {
    Livewire.on('startTimer', () => {
        // Timer is handled by Livewire
    });
});