function call_alert(){
    alert("You need to be logged in to place an order!");
}

function validateCreditCard(number){
    var regex = new RegExp("^[0-9]{16}$");
    return regex.test(number);
}

function validatePassword(password){
    var regex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
    return regex.test(password);
}

function validateEmail(email){
    var regex = new RegExp("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$");
    return regex.test(email);
}