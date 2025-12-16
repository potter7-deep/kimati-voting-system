document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initModals();
    initForms();
});

function initTheme() {
    const themeToggle = document.getElementById('themeToggle');
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
    }
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
        });
    }
}

function initModals() {
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
            event.target.style.display = 'none';
        }
    });
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function switchModal(modalId) {
    const allModals = document.querySelectorAll('.modal');
    allModals.forEach(modal => {
        modal.classList.remove('show');
        modal.style.display = 'none';
    });
    showModal(modalId);
}

function initForms() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
}

function handleLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.role === 'admin' ? 'dashboard.php' : 'vote.php';
        } else {
            alert('Login failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during login');
    });
}

function handleRegister(e) {
    e.preventDefault();
    
    const password = document.getElementById('registerPassword').value;
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            switchModal('loginModal');
            document.getElementById('registerForm').reset();
        } else {
            alert('Registration failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during registration');
    });
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

window.addEventListener('beforeunload', function() {
    const modals = document.querySelectorAll('.modal.show');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
    document.body.style.overflow = 'auto';
});

function handleResponsive() {
    const width = window.innerWidth;
    const navMenu = document.querySelector('.navbar-menu');
    
    if (width < 768 && navMenu) {
        const items = navMenu.querySelectorAll('.nav-link');
        items.forEach(item => {
            item.style.fontSize = '0.875rem';
        });
    }
}

window.addEventListener('resize', debounce(handleResponsive, 250));
handleResponsive();

function switchTab(e, tabName) {
    e.preventDefault();
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');
}

function updateElectionStatus(electionId) {
    const status = document.getElementById('electionStatus').value;
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('election_id', electionId);
    formData.append('status', status);
    
    fetch('admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteCandidate(candidateId) {
    if (confirm('Are you sure you want to remove this candidate?')) {
        const formData = new FormData();
        formData.append('action', 'delete_candidate');
        formData.append('candidate_id', candidateId);
        
        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function castVote(electionId, coalitionId) {
    const formData = new FormData();
    formData.append('action', 'vote');
    formData.append('election_id', electionId);
    formData.append('coalition_id', coalitionId);
    
    fetch('voting.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        alert(data.message);
        if (data.success) {
            closeModal('votingModal');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error casting vote: ' + error.message);
    });
}

function finishAddingMembers() {
    closeModal('quickAddMemberModal');
    location.reload();
}

document.addEventListener('submit', function(e) {
    if (e.target.id === 'addCoalitionForm') {
        e.preventDefault();
        const formData = new FormData(e.target);
        const electionId = formData.get('election_id');
        const coalitionName = formData.get('name');
        
        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('addCoalitionModal');
                const coalitionId = data.coalition_id;
                document.getElementById('addMemberCoalitionId').value = coalitionId;
                document.getElementById('addMemberElectionId').value = electionId;
                document.getElementById('addMemberCoalitionName').textContent = coalitionName;
                document.getElementById('membersList').innerHTML = '';
                document.getElementById('addMemberForm').reset();
                showModal('quickAddMemberModal');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating coalition');
        });
    } else if (e.target.id === 'addMemberForm') {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const memberName = formData.get('name');
                const position = formData.get('position');
                const positionLabel = position.replace(/_/g, ' ').split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                const memberItem = document.createElement('li');
                memberItem.className = 'member-item';
                memberItem.textContent = positionLabel + ': ' + memberName;
                document.getElementById('membersList').appendChild(memberItem);
                e.target.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding member');
        });
    } else if (e.target.classList.contains('addMemberForm')) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
