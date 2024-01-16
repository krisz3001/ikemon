let cardContainer = document.querySelector("#card-list");
let filterSelect = document.querySelector("#filter-type");

filterSelect.addEventListener("change", e => {
    let type = e.target.value;
    fetch(`/page.php?page=0&type=${type}`, {headers: {"fetch": true}})
        .then(res => res.text())
        .then(r => cardContainer.innerHTML = r);
    fetch(`/pager.php?type=${type}`, {headers: {"fetch": true}})
        .then(res => res.text())
        .then(r => pager.innerHTML = r);
});