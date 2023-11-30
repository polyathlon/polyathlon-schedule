function competitionNameFilter(event) {
    const competitionName = event.target.value;
    const polyFilter = event.target.parentNode;
    const startTime = polyFilter.querySelector('#competition-time').valueAsDate;
    const competitionStatus = polyFilter.querySelector('#competition-status').value;

    competitionTimeHider(startTime);
    let list;
    if (startTime === null)
        list = listASC;
    else
        list = (startTime.toISOString().split('T')[0]===(new Date()).toISOString().split('T')[0])? listNative : listASC;
    competitionStatusHider(competitionStatus, startTime, list);
    competitionNameHider(competitionName, list);

    insertNodes(list);
}

function competitionNameHider(competitionName, list) {
    if (competitionName === '--Соревнование--')
        return;
    list.forEach((item) => {
        if (!item.querySelector('span.poly-competition-name')?.innerText.trim().includes(competitionName))
            item.hidden = true;
    })
}

function competitionStatusFilter(event) {
    const competitionStatus = event.target.value;
    const polyFilter = event.target.parentNode;
    const competitionName = polyFilter.querySelector('#competition-name').value;
    const startTime = polyFilter.querySelector('#competition-time').valueAsDate;

    competitionTimeHider(startTime);
    let list;
    if (startTime === null)
        list = listASC;
    else
        list = (startTime.toISOString().split('T')[0]===(new Date()).toISOString().split('T')[0])? listNative : listASC;

    competitionStatusHider(competitionStatus, startTime, list);
    competitionNameHider(competitionName, list);
    insertNodes(list);
}

function competitionStatusHider(competitionStatus, startTime, list) {
    if (!startTime) {
        startTime = new Date('2023-01-01');
    }
    const nextStartTime = new Date(startTime);
    nextStartTime.setMonth(nextStartTime.getMonth() + 1);
    switch(competitionStatus) {
        case 'Текущее':
            list.forEach((item) => {
                const competitionDate = item.querySelector('.poly-dates span');
                const competitionStartTime = competitionDate?.dataset.start;
                const competitionEndTime = competitionDate?.dataset.end;
                item.hidden = startTime > new Date(competitionEndTime) || startTime < new Date(competitionStartTime)
            })
            break;
        case 'Прошедшее':
            list.forEach((item) => {
                const competitionDate = item.querySelector('.poly-dates span');
                const competitionEndTime = competitionDate?.dataset.end;
                item.hidden = startTime <= new Date(competitionEndTime);
            })
            break;
        case 'Ближайшее':
            list.forEach((item) => {
                const competitionDate = item.querySelector('.poly-dates span');
                const competitionStartTime = competitionDate?.dataset.start;
                item.hidden = startTime > new Date(competitionStartTime) || new Date(competitionStartTime) > nextStartTime;
            })
    }
}

// function competitionTimeFilter(event) {

//     //a.toISOString().split('T')[0]
//     const startTime = event.target.valueAsDate;
//     const polyFilter = event.target.parentNode;
//     const competitionName = polyFilter.querySelector('#competition-name').value;
//     const competitionStatus = polyFilter.querySelector('#competition-status').value;

//     let list = document.querySelector('.poly-items');
//     if (list === null)
//         return;

//     if (startTime === null) {
//         [...list.children].forEach((item) => {
//             if (item.hidden) {
//                 item.hidden = false;
//             }
//         })
//     }
//     else {
//         [...list.children].forEach((item) => {
//             const competitionDate = item.querySelector('.poly-dates span');
//             const competitionStartTime = competitionDate?.dataset.start;
//             let competitionEndTime = competitionDate?.dataset.end;
//             item.hidden = startTime > new Date(competitionEndTime);
//         })
//     }
// }

let listASC;

function sortASC(event) {
    const nodeList = document.querySelector('.poly-items');
    if (nodeList === null)
        return;
    listNative ??= [...nodeList.children];
    listASC = [...nodeList.children];
    listASC.sort((a, b) => {
        const competitionDateA = a.querySelector('.poly-dates span');
        const competitionStartTimeA = competitionDateA?.dataset.start;
        const competitionDateB = b.querySelector('.poly-dates span');
        const competitionStartTimeB = competitionDateB?.dataset.start;
        if (competitionStartTimeA === competitionStartTimeB) {
            const competitionEndTimeA = competitionDateA?.dataset.end;
            const competitionEndTimeB = competitionDateB?.dataset.end;
            if (competitionEndTimeA === competitionEndTimeB) {
                const competitionNameA = a.querySelector('.poly-competition-name')?.innerText;
                const competitionNameB = b.querySelector('.poly-competition-name')?.innerText;
                if (competitionNameA === competitionNameB)
                    return 0;
                return competitionNameA < competitionNameB ? -1 : 1;
            }
            return competitionEndTimeA < competitionEndTimeB ? -1 : 1;
        }
        return competitionStartTimeA < competitionStartTimeB ? -1 : 1
    });
    return listASC;
}

let listDesc;

function sortDESC(event) {
    const nodeList = document.querySelector('.poly-items');
    if (nodeList === null)
        return;
    listDesc = [...nodeList.children];
    listDesc.sort((a, b) => {
        const competitionDateA = a.querySelector('.poly-dates span');
        const competitionStartTimeA = competitionDateA?.dataset.start;
        const competitionDateB = b.querySelector('.poly-dates span');
        const competitionStartTimeB = competitionDateB?.dataset.start;
        if (competitionStartTimeA === competitionStartTimeB) {
            const competitionEndTimeA = competitionDateA?.dataset.end;
            const competitionEndTimeB = competitionDateB?.dataset.end;
            if (competitionEndTimeA === competitionEndTimeB) {
                const competitionNameA = a.querySelector('.poly-competition-name')?.innerText;
                const competitionNameB = b.querySelector('.poly-competition-name')?.innerText;
                if (competitionNameA === competitionNameB)
                    return 0;
                return competitionNameA > competitionNameB ? -1 : 1;
            }
            return competitionEndTimeA > competitionEndTimeB ? -1 : 1;
        }
        return competitionStartTimeA > competitionStartTimeB ? -1 : 1
    });
    return listDesc;
}

let listNative;

function sortNative(event) {
    const nodeList = document.querySelector('.poly-items');
    if (nodeList === null)
        return;
    return listNative = [...nodeList.children];
}

function competitionTimeFilter(event) {

    const startTime = event.target.valueAsDate;
    const polyFilter = event.target.parentNode;
    const competitionName = polyFilter.querySelector('#competition-name').value;
    const competitionStatus = polyFilter.querySelector('#competition-status').value;

    competitionTimeHider(startTime);
    let list;
    if (startTime === null)
        list = listASC;
    else
        list = (startTime.toISOString().split('T')[0]===(new Date()).toISOString().split('T')[0])? listNative : listASC;
    competitionStatusHider(competitionStatus, startTime, list);
    competitionNameHider(competitionName, list);
    insertNodes(list);
}

function insertNodes(list) {
    const nodeList = document.querySelector('.poly-items');
    list.forEach((item) => {
        nodeList?.appendChild(item);
    })
}

function competitionTimeHider(startTime) {
    if (startTime === null) {
        const list = listASC || sortASC();
        list.forEach((item) => {
            if (item.hidden) {
                item.hidden = false;
            }
        })
    }
    else if (startTime.toISOString().split('T')[0] === (new Date()).toISOString().split('T')[0]) {
        const list = listNative || sortNative();
        list.forEach((item) => {
            if (item.hidden) {
                item.hidden = false;
            }
        })
    }
    else {
        const list = listASC || sortASC();
        list.forEach((item) => {
            const competitionDate = item.querySelector('.poly-dates span');
            // const competitionStartTime = competitionDate?.dataset.start;
            let competitionEndTime = competitionDate?.dataset.end;
            item.hidden = startTime > new Date(competitionEndTime);
        })
    }
}

    // items.sort((a , b) => {
    //     if (a.querySelector('span.poly-competition-name')?.innerText >
    //         b.querySelector('span.poly-competition-name')?.innerText)
    //         return 1;
    //     if (a.querySelector('span.poly-competition-name')?.innerText <
    //         b.querySelector('span.poly-competition-name')?.innerText)
    //         return -1;
    //     if (a.querySelector('poly-competition-number')?.innerText >
    //         b.querySelector('poly-competition-number')?.innerText)
    //         return 1;
    //     return 0;
    // }).forEach(node => list?.appendChild(node))

