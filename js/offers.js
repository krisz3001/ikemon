// Sending side

let cardFirst = document.querySelector("#offer-card-first");
let cardSecond = document.querySelector("#offer-card-second");
let offerSelect = document.querySelector("#offer-select");
let getting = document.querySelector("#getting");

let payThis = document.querySelector("#pay-this");
let payThat = document.querySelector("#pay-that");

if (modalOffer) {
    modalOffer.addEventListener("click", hideOfferModal);
    offerSelect.addEventListener("change", () => {
        let giveCardId = offerSelect.options[offerSelect.selectedIndex].value;
        fetch(`/card.php?id=${giveCardId}`, {headers: {"fetch": true}})
            .then(res => res.text())
            .then(r => cardSecond.innerHTML = r);
        offerSelect.classList.add("offer-selected");
        offerSelect.classList.add("padding-off");
    });

    if (activeModal === modalOffer) {
        let getCardId = getting.value;
        if (getCardId) {
            fetch(`/card.php?id=${getCardId}`, {headers: {"fetch": true}})
                .then(res => res.text())
                .then(r => cardFirst.innerHTML = r);
        }
        let giveCardId = offerSelect.options[offerSelect.selectedIndex].value;
        if (giveCardId) {
            fetch(`/card.php?id=${giveCardId}`, {headers: {"fetch": true}})
                .then(res => res.text())
                .then(r => cardSecond.innerHTML = r);
            offerSelect.classList.add("offer-selected");
            offerSelect.classList.add("padding-off");
        }
    }
    window.addEventListener("keydown", hideOfferModal);
}

cardList.addEventListener("click", e => {
    let elem = e.target.closest(".btn-trade");
    if (!elem) return;
    fetch(`/card.php?id=${elem.dataset.id}`, {headers: {"fetch": true}})
        .then(res => res.text())
        .then(r => cardFirst.innerHTML = r);
    getting.value = elem.dataset.id;
    offerSelect.selectedIndex = 0;
    payThis.value = "0";
    payThat.value = "0";
    showModal(modalOffer);
});

function hideOfferModal(e) {
    hideModal(e);
    if (!activeModal) {
        cardSecond.innerHTML = "";
        offerSelect.classList.remove("offer-selected");
        offerSelect.classList.remove("padding-off");
        offerSelect.selectedIndex = 0;
    }
}

// Receiving side

let requestCardLeft = document.querySelector("#request-card-first");
let requestCardRight = document.querySelector("#request-card-second");
let requestReplies = document.querySelector("#request-replies");
let requestAccept = document.querySelector("#request-accept");
let requestDeny = document.querySelector("#request-deny");
let requestConfirm = document.querySelector("#btn-request-confirm");

let requestPay = document.querySelector("#request-pay");
let requestReceive = document.querySelector("#request-receive");

let requestID = document.querySelector("#request-id");
let requestAnswer = document.querySelector("#request-answer");

let replyElem = null;
let reply = null;

if (modalRequest) {
    modalRequest.addEventListener("click", hideModal);
    requestClose.addEventListener("click", hideModal);
    notifications.addEventListener("click", e => {
        let elem = e.target.closest(".notification");
        if (!elem) return;
        let data = elem.dataset;
        let giveCardId = data.give;
        fetch(`/card.php?id=${giveCardId}`, {headers: {"fetch": true}})
            .then(res => res.text())
            .then(r => requestCardLeft.innerHTML = r);
        let getCardId = data.get;
        fetch(`/card.php?id=${getCardId}`, {headers: {"fetch": true}})
            .then(res => res.text())
            .then(r => requestCardRight.innerHTML = r);
        replyElem = null;
        reply = null;
        requestConfirm.innerHTML = "";
        requestConfirm.classList.add("disabled");
        requestAccept.classList.remove("reply-selected");
        requestDeny.classList.remove("reply-selected");
        requestPay.innerHTML = data.receive;
        requestReceive.innerHTML = data.pay;
        requestID.value = data.id;
        showModal(modalRequest);
    });
}

if (requestReplies) {
    requestReplies.addEventListener("click", e => {
        if (!e.target.matches(".request-reply")) return;
        if (replyElem) replyElem.classList.remove("reply-selected");
        e.target.classList.add("reply-selected");
        replyElem = e.target;
        reply = e.target.dataset.reply;
        requestConfirm.classList.remove("disabled");
        if (reply === "accept") requestConfirm.innerHTML = "We got a deal";
        if (reply === "deny") requestConfirm.innerHTML = "Nope";

        requestAnswer.value = reply;
    })
}