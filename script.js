 function clearFields() {
     document.getElementById('stockcode').value = '';
     document.getElementById('productclass').value ='';
     document.getElementById('warehouse').value = '';
     document.getElementById('quantity').value = '';
     document.getElementById('transaction_type').value = '';
}
document.getElementById('undo').addEventListener('click', clearFields);
