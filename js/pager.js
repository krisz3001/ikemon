let pager = document.querySelector(".pager");
let container = document.querySelector("#card-list");

pager.addEventListener("click", e => {
    if (!e.target.matches(".pager button")) return;
    if (e.target.matches(".pager-active")) return;

    let index = e.target.dataset.index;
    fetch(`page.php?page=${index}`, {headers: {"fetch": true}})
        .then(res => res.text())
        .then(r => {
            container.innerHTML = r;
            let active = document.querySelector(".pager-active");
            if (active) active.classList.remove("pager-active");
            e.target.classList.add("pager-active");
        });
});