const API = '../api.php';

function register(){
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;

    fetch(API, {method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({action:'register', name, email, password})})
    .then(res=>res.json())
    .then(data=>{
        if(data.status==='success') alert('Registered Successfully!');
        else alert(data.msg);
    });
}

function login(){
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    fetch(API, {method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({action:'login', email, password})})
    .then(res=>res.json())
    .then(data=>{
        if(data.status==='success'){
            localStorage.setItem('user_id', data.user.user_id);
            localStorage.setItem('user_name', data.user.name);
            window.location.href='home.html';
        } else alert(data.msg);
    });
}
