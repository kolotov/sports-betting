

document.addEventListener("DOMContentLoaded", (event) => { 

updateRate();

});


const updateRate = () => {
    
    //1.01 to 40.00
    const rnd = (max, min) => {
        min = min * 100;
        max = max * 100;
        return (Math.floor((Math.random()  * (max - min - 1) + min + 1)) / 100);
    };
   
    let ratio = rnd(1.01, 40.00);
    document.getElementById("win-first").innerHTML = ratio;
    document.getElementById("win-first").setAttribute('data-ratio', ratio);
    
    ratio = rnd(1.01, 40.00);
    document.getElementById("win-noone").innerHTML = ratio;
    document.getElementById("win-noone").setAttribute('data-ratio', ratio);
    
    ratio = rnd(1.01, 40.00);
    document.getElementById("win-second").innerHTML = ratio;
    document.getElementById("win-second").setAttribute('data-ratio', ratio);
    

};


const sendMsg = (msg) => {
        if ((String(msg).length) > 0) {
            document.getElementById('msg-label').innerHTML = msg;
            document.getElementById('msg-label').style.setProperty('display', 'block', 'important');
            setTimeout( () => { 
                document.getElementById('msg-label').style.setProperty('display', 'none', 'important'); 
            }, 2000);
        }
};

const renderUserList = (users) => {
    const sel = document.getElementById("user-id");

    users.forEach((user) => {
        let opt = document.createElement("option");
        opt.value = user.user_id;
        opt.text = user.user_name;
        sel.add(opt, null);
    });
};

const renderUserBalance = (balance) => {
    document.getElementById('user-balance').value = balance;
}
