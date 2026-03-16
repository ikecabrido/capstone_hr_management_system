document.getElementById('sidebarToggle').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.flex-grow-1');
    
    sidebar.classList.toggle('w-64');  
    sidebar.classList.toggle('w-16'); 

    if (sidebar.classList.contains('w-64')) {
        mainContent.classList.add('ml-64');  
    } else {
        mainContent.classList.remove('ml-64');  
    }
});