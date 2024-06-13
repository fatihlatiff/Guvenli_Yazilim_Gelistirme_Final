// Example AJAX function to search users asynchronously
function searchUser() {
    const username = document.querySelector('input[name="username"]').value;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'search.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.querySelector('#results').innerHTML = this.responseText;
        }
    };
    xhr.send('username=' + username);
}