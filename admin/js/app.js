oneCheckToall = document.querySelector('#checkAllBox');
allCheckBoxToCheck = document.querySelectorAll('input[type=checkbox]#checkItem');
if (oneCheckToall && allCheckBoxToCheck) {
    allCheckBoxToCheck = Array.from(allCheckBoxToCheck);
    oneCheckToall.addEventListener('change', (e) => {
        let isChecked = e.target.checked;
        allCheckBoxToCheck.forEach(checkBox => {
            checkBox.checked = isChecked;
        });
    })
}