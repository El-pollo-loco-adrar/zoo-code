
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.reponse-btn').forEach(button => {
        button.addEventListener('click', function () {
            this.classList.add('clicked');

            document.getElementById('hidden-reponse').value = this.dataset.reponse;

            document.querySelectorAll('.reponse-btn').forEach(btn => btn.disabled = true);

            setTimeout(() => {
                document.getElementById('form-question').submit();
            }, 1500);
        });
    });
});
console.log("test");