$(function () {
    const getMessage = () => "Hello World";
    if (document.getElementById('output'))
        document.getElementById('output').innerHTML = getMessage();
});