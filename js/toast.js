// excuse this mess

let toasts = document.querySelector("#toasts");
let children = Array.from(toasts.children);

const TOAST_HEIGHT = 80;
const MARGIN = 20;

let mouseOverToast = -1;
let delays = [];

toasts.addEventListener("click", e => {
    let toast = e.target.closest(".toast");
    if (!toast) return;
    let index = children.findIndex(x => x.dataset.id === toast.dataset.id);
    toast.style.display = "none";
    mouseOverToast = -1;
    moveToastsFrom(index);
    clearTimeout(delays[index]);
});


// Stops hiding the hovered toast
toasts.addEventListener("mouseover", e => {
    let toast = e.target.closest(".toast");
    if (!toast) return;
    let index = +toast.dataset.id;
    clearTimeout(delays[index]);
    mouseOverToast = index;
    toast.style.opacity = 1;
});

// Starts a timeout for the toast the mouse left
toasts.addEventListener("mouseleave", () => {
    let current = mouseOverToast;
    if (current === -1) return;
    newHidingTimeout(current)
    mouseOverToast = -1;
});

// Initializing hiding on load
children.forEach((_, i) => newHidingTimeout(i));


// Starts a timeout to hide a toast
function newHidingTimeout(index) {    
    clearTimeout(delays[index]);
    delays[index] = setTimeout(() => {
        let actualIndex = children.findIndex(x => x.dataset.id == index);
        let style = children[actualIndex].style;
        style.opacity = 1;
        let interval = setInterval(() => {
            if (mouseOverToast === index) {
                clearInterval(interval);
                return;
            }
            style.opacity -= 0.2;
                if (style.opacity < 0 && style.display != "none") {
                    style.display = "none";
                    moveToastsFrom(children.findIndex(x => x.dataset.id == index));
                    clearInterval(interval);
                }
                if (style.display === "none") clearInterval(interval);
        }, 100);
    }, 1000*(children.findIndex(x => x.dataset.id == index)+3));
}

// Repositions toasts from given index
function moveToastsFrom(index) {
    children.forEach((elem, i) => {
        if (i > index) {
            elem.style.bottom = (TOAST_HEIGHT + MARGIN) * (i-1) + MARGIN + "px";
        }
    })
    children.splice(index, 1);
}