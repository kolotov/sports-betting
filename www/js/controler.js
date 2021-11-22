
document.addEventListener("DOMContentLoaded", (event) => { 
    getUsersList();

    document.getElementById("win-first").onmousedown = (e) => actionBet(e);
    document.getElementById("win-noone").onmousedown = (e) => actionBet(e);
    document.getElementById("win-second").onmousedown = (e) => actionBet(e);
   
    document.getElementById("user-id").onchange = (e) => getBalance();
    document.getElementById("bet-currency").onchange = (e) => getBalance();
});

const actionBet = (e) => {
 return new Promise((resolve, reject) => {    
    const action_el = e.target;
    const user_el =  document.getElementById("user-id");
    const currency_el = document.getElementById("bet-currency");
    const sum_el =  document.getElementById("bet-sum");
    const winner_el = document.getElementById("winner"); 

   if ((action_el.hasAttribute("data-ratio") === false) ||
       (action_el.hasAttribute("data-result") === false)) {
    throw new Error("Нарушена верстка страницы");
   }
 
    const user_id = user_el.value;
    if( (user_id > 0) === false) {
        throw new Error("Выберите пользователя!");
    }

   
    let data = {};
    let bet = {};
    
    const ratio = action_el.getAttribute("data-ratio");
    bet.ratio = ratio;
    
    const result = action_el.getAttribute("data-result");
    bet.result = parseInt(result);

    if(
        currency_el.value != 'usd' && 
        currency_el.value != 'rub' && 
        currency_el.value != 'eur'
    ) {
        throw new Error("Выберите валюту!");
    }
    bet.currency = currency_el.value;

    if( sum_el.value < 1 || sum_el.value > 500) {
        throw new Error("Сумма ставки должна быть в пределах от 1 до 500 денежных единиц");
    }
    bet.sum = sum_el.value;
 
    data.bet = bet;
    
    if (winner_el.checked === false) {
        data.winner =  bet.result > 0 ? (bet.result - 1) : 2;
    } else  {
        data.winner = bet.result;
    }

    const url = `/api/users/${user_id}/action/bet/`;
    const method = 'PUT';
    resolve(sendRequest(method, url, data));
})
   .then(result => getBalance())
   .catch(error => sendMsg(error));
};

    const sendRequest = async (method, url, data = {}) => {
    
    const params = {
        PUT: { 
                method: 'PUT',
                body: JSON.stringify(data), 
                headers: {
                            'Content-Type': 'application/json;charset=utf-8'
                }
            },
       GET: { 
                method: 'GET',
                headers: {
                            'Content-Type': 'application/json;charset=utf-8'
                }
            }
  
    };

    const response = await fetch(url, params[method]);

    const json = await response.json();
    
    if (response.ok) {
        sendMsg(''); 
    }else {
        throw new Error(json.error.message);
    }
    if (method === 'GET') {
        return json;
    }
};



const getUsersList = ( ) => { 
    return new Promise ((resolve, reject) => {
    const url = `/api/users/action/list/`;
    const method = 'GET';
    resolve(sendRequest(method, url));
    // reject(new Error("Error!"));
    })
    .then(result => renderUserList(result.result))
    .catch(error => sendMsg(error));
}


const getBalance = () => {
    return new Promise ((resolve, reject) => {
    const user_id = document.getElementById("user-id").value;
    const currency = document.getElementById("bet-currency").value;

    if( (user_id > 0) === false) {
        throw new Error("Выберите пользователя!");
    }
    
    if(currency != 'usd' && currency != 'rub' && currency != 'eur') {
        throw new Error("Выберите валюту!");
    }


    const url = `/api/users/${user_id}/action/balance/${currency}/`;
    const method = 'GET';
    resolve(sendRequest(method, url));
    // reject(new Error("Error!"));
    })
    .then(result => renderUserBalance(result.result))
    .catch(error => sendMsg(error));
}


