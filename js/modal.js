let modalLogin = document.querySelector("#modal-login");
let modalRegister = document.querySelector("#modal-register");
let modalTrade = document.querySelector("#modal-trade");
let modalNewCard = document.querySelector("#modal-new-card");
let modalRandom = document.querySelector("#modal-random");
let modalEdit = document.querySelector("#modal-edit");
let modalOffer = document.querySelector("#modal-offer");
let modalRequest = document.querySelector("#modal-request");

let loginShow = document.querySelector("#btn-login-show");
let loginClose = document.querySelector("#btn-login-close");
let registerShow = document.querySelector("#btn-register-show");
let registerClose = document.querySelector("#btn-register-close");

let cardList = document.querySelector("#card-list");

let tradeCard = document.querySelector("#trade-card");
let tradePrice = document.querySelector("#trade-price");
let tradeOwner = document.querySelector("#trade-owner");
let tradeClose = document.querySelector("#btn-close");
let tradeId = document.querySelector("#trade-id");

let editName = document.querySelector("#edit-name");
let editImage = document.querySelector("#edit-image");
let editHp = document.querySelector("#edit-hp");
let editAttack = document.querySelector("#edit-attack");
let editDefense = document.querySelector("#edit-defense");
let editPrice = document.querySelector("#edit-price");
let editOwner = document.querySelector("#edit-owner");
let editDescription = document.querySelector("#edit-description");
let editType = document.querySelector("#edit-type");
let editId = document.querySelector("#edit-id");

let newCardShow = document.querySelector("#btn-new-card");
let newCardClose = document.querySelector("#btn-add-close");

let randomShow = document.querySelector("#btn-random-card");
let randomClose = document.querySelector("#btn-random-close");

let editClose = document.querySelector("#btn-edit-close");
let offerClose = document.querySelector("#btn-offer-close");
let requestClose = document.querySelector("#btn-request-close");

let activeModal = document.querySelector(".active");

if (activeModal) {
    let inp = activeModal.querySelector("input");
    inp.focus();
    if (inp.type === "number") inp.select();
    else inp.setSelectionRange(inp.value.length, inp.value.length);
}


function showModal(modal) {
    modal.style.display = "flex";
    let inp = modal.querySelector("input");
    if (inp.type === "number") inp.select();
    if (inp.type != "hidden") inp.focus();
    else modal.querySelector("button").focus();
    activeModal = modal;
    document.documentElement.classList.add("noscroll");
}

let target;

window.addEventListener("mousedown", e => {
    target = e.target;
})


function hideModal(e) {
    if (e.type === "keydown" && e.key != "Escape") return;
    if (e.type === "click" && e.target != target) return;
    if (!activeModal) return;
    if (e.type === "click" && !([loginClose, registerClose, activeModal, tradeClose, newCardClose, randomClose, editClose, offerClose, requestClose].includes(e.target))) return;
    e.stopPropagation();
    e.preventDefault();
    document.documentElement.classList.remove("noscroll");
    activeModal.style.display = "none";
    activeModal.classList.remove("active");
    activeModal = null;
}

if (loginShow && registerShow) {
    loginShow.addEventListener("click", () => showModal(modalLogin));
    registerShow.addEventListener("click", () => showModal(modalRegister));
}

if (modalLogin && modalRegister) {
    modalLogin.addEventListener("click", hideModal);
    modalRegister.addEventListener("click", hideModal);
    loginClose.addEventListener("click", hideModal);
    registerClose.addEventListener("click", hideModal);
}
modalTrade.addEventListener("click", hideModal);


window.addEventListener("keydown", hideModal);

cardList.addEventListener("click", e => {
    let elem = e.target.closest(".trade");
    if (!elem) return;
    let data = e.target.closest(".trade").dataset;
    if (data.owner) {
        tradeOwner.innerHTML = data.owner;
        if (data.owner === "admin") tradeOwner.classList.add("admin");
        else tradeOwner.classList.remove("admin");
    }
    tradeCard.innerHTML = data.name;
    tradePrice.innerHTML = data.price;
    tradeId.value = data.id;
    showModal(modalTrade);
});

if (modalNewCard) {
    modalNewCard.addEventListener("click", hideModal);
    newCardShow.addEventListener("click", () => showModal(modalNewCard));
    newCardClose.addEventListener("click", hideModal);
}

if (modalRandom) {
    modalRandom.addEventListener("click", hideModal);
    randomShow.addEventListener("click", () => showModal(modalRandom));
    randomClose.addEventListener("click", hideModal);
}

if (modalEdit) {
    modalEdit.addEventListener("click", hideModal);
    editClose.addEventListener("click", hideModal);
    cardList.addEventListener("click", e => {
        if (e.target.matches(".btn-edit")) {
            let data = JSON.parse(e.target.dataset.card);
            let editErrors = document.querySelector("#edit-errors");
            if (editErrors) {
                editErrors.innerHTML = "";
                editErrors.style.display = "none";
            }
            editName.value = data.name;
            editDescription.value = data.description;
            editPrice.value = data.price;
            editHp.value = data.hp;
            editAttack.value = data.attack;
            editDefense.value = data.defense;
            editOwner.value = data.owner;
            editImage.value = data.image;
            editType.selectedIndex = data.type;
            editId.value = data.id;
            showModal(modalEdit);
        }
    });
}


// Prevent dragging
window.addEventListener("dragstart", e => {
    if (!e.target.matches || e.target.matches("img, a")) e.preventDefault();
});