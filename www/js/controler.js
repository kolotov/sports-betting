
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
    let data = {};
    let bet = {};    


   if ((e.target.hasAttribute("data-ratio") === false) ||
       (e.target.hasAttribute("data-result") === false)) {
    throw new Error("Нарушена верстка страницы");
   }
    
    const ratio = e.target.getAttribute("data-ratio");
    bet.ratio = ratio;
    
    const result = e.target.getAttribute("data-result");
    bet.result = parseInt(result);


    const currency = document.getElementById("bet-currency").value;
    if (currency != 'usd' && currency != 'rub' && currency != 'eur') {
        throw new Error("Выберите валюту!");
    }
    bet.currency = currency;

    const sum = Number(document.getElementById("bet-sum").value);
    
    if (isNaN(sum)) {
        throw new Error("Вы не задали сумму ставки");
    }
     
    if (sum < 1 || sum > 500 || sum != Math.round(sum)) {
        throw new Error("Сумма ставки должна целым числом в пределах от 1 до 500 денежных единиц");
    }
     
    const balance = Number(document.getElementById("user-balance").value);
    if (sum > balance) {
        throw new Error("На балансе недостаточно средств");
    } 
    bet.sum = sum;
     
    
    const winner = document.getElementById("winner").checked; 
    if (winner === false) {
        data.winner =  bet.result > 0 ? (bet.result - 1) : 2;
    } else  {
        data.winner = bet.result;
    }

    const user_id = Number(document.getElementById("user-id").value);

    if (user_id <= 0 || isNaN(user_id)) {
        throw new Error("Выберите пользователя!");
    }

    const url = `/api/users/${user_id}/action/bet/`;
    const method = 'PUT';
    data.bet = bet;
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
    
    if (response.ok === false ) {
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


