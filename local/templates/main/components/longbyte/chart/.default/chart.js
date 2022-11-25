function mark(i, n, markAll, type, dec) {
//Если навели на пустое значение, выходим
    if (typeof (data[i][n][serviceCount]) == 'undefined')
        return;

//Разбираемся с группами
    var gnum = -1;
    for (j = 0; j < groups.length; j++)
        for (k = 0; k < groups[j].length; k++)
            if (groups[j][k] == i) {
                gnum = j;
                break
            }
    if (gnum == -1) {
        var grp = Array(1);
        grp[0] = i
    } else {
        var grp = Array(groups[gnum].length);
        for (var k = 0; k < groups[gnum].length; k++)
            grp[k] = groups[gnum][k]
    }
//Заполняем номера полей, по которым надо пройтись
    var grpJ = Array(grp.length);
    for (var g = 0; g < grp.length; g++) {
        grpJ[g] = -1;
        for (var j = 0; j < data[grp[g]].length; j++)
            if (data[grp[g]][j][0] == data[i][n][0]) {
                grpJ[g] = j;
                break
            }
    }
//Главный цикл по группе графиков
    for (var g = 0; g < grp.length; g++)
        if (grpJ[g] >= 0 || type == 3 || type == 4) {
            for (var j = 0; j < data[grp[g]].length; j++) {
                //Создаем хранилище для знаков
                var sign = Array(data[grp[g]][j].length - serviceCount);
                for (var k = serviceCount; k < data[grp[g]][j].length; k++)
                    if (document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)) != null) {
                        if (type == 3 || type == 4)
                            sign[k - serviceCount] = data[i][n][k] - data[grp[g]][j][k]
                        else
                            sign[k - serviceCount] = data[grp[g]][grpJ[g]][k] - data[grp[g]][j][k];
                        if (document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML != '&nbsp;' && type > 0)
                            if ((grp[g] == i && j == n) || (type != 3 && type != 4 && j == grpJ[g]))
                                document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = data[grp[g]][j][k]
                            else
                                switch (type) {
                                    case (1):
                                        if (data[grp[g]][grpJ[g]][k] != 0)
                                            if (sign[k - serviceCount] < 0)
                                                document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = '+' + Math.round((data[grp[g]][j][k] / data[grp[g]][grpJ[g]][k] - 1) * 100 * Math.pow(10, dec)) / Math.pow(10, dec) + '%'
                                            else
                                                document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = Math.round((data[grp[g]][j][k] / data[grp[g]][grpJ[g]][k] - 1) * 100 * Math.pow(10, dec)) / Math.pow(10, dec) + '%'
                                        else
                                            document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = 'inf.';
                                        break;
                                    case (2):
                                        if (sign[k - serviceCount] < 0)
                                            document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = '+' + Math.round(-sign[k - serviceCount] * Math.pow(10, dec)) / Math.pow(10, dec)
                                        else
                                            document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = -Math.round(sign[k - serviceCount] * Math.pow(10, dec)) / Math.pow(10, dec);
                                        break;
                                    case (3):
                                        if (data[i][n][k] != 0)
                                            if (sign[k - serviceCount] < 0)
                                                document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = '+' + Math.round((data[grp[g]][j][k] / data[i][n][k] - 1) * 100 * Math.pow(10, dec)) / Math.pow(10, dec) + '%'
                                            else
                                                document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = Math.round((data[grp[g]][j][k] / data[i][n][k] - 1) * 100 * Math.pow(10, dec)) / Math.pow(10, dec) + '%'
                                        else
                                            document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = 'inf.';
                                        break;
                                    case (4):
                                        if (sign[k - serviceCount] < 0)
                                            document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = '+' + Math.round(-sign[k - serviceCount] * Math.pow(10, dec)) / Math.pow(10, dec)
                                        else
                                            document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = -Math.round(sign[k - serviceCount] * Math.pow(10, dec)) / Math.pow(10, dec);
                                        break;
                                }
                    }
                //Сохраняем первый знак и сравниваем с ним остальные
                var good = true;
                if (sign.length > 1)
                    for (var k = 1; k < sign.length; k++)
                        if (!((sign[0] >= 0 && sign[k] >= 0) || (sign[0] <= 0 && sign[k] <= 0))) {
                            good = false;
                            break
                        }
                //Если все знаки одинаковые, меняем цвет
                if (good && markAll)
                    if (sign[0] >= 0)
                        document.getElementById('c' + grp[g] + 'r' + j).className = "below"
                    else
                        document.getElementById('c' + grp[g] + 'r' + j).className = "above";
            }
            if (grpJ[g] >= 0 && ((type != 3 && type != 4) || grp[g] == i))
                document.getElementById('c' + grp[g] + 'r' + grpJ[g]).className = "active";
        }
}

function unmark(i) {
    var gnum = -1;
    for (var j = 0; j < groups.length; j++)
        for (var k = 0; k < groups[j].length; k++)
            if (groups[j][k] == i) {
                gnum = j;
                break
            }
    if (gnum == -1) {
        var grp = Array(1);
        grp[0] = i
    } else {
        var grp = Array(groups[gnum].length);
        for (var k = 0; k < groups[gnum].length; k++)
            grp[k] = groups[gnum][k]
    }
    for (var g = 0; g < grp.length; g++) {
        for (var j = 0; j < data[grp[g]].length; j++) {
            for (var k = serviceCount; k < data[grp[g]][j].length; k++)
                if (document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)) != null)
                    if (document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML != '&nbsp;')
                        document.getElementById('c' + grp[g] + 'd' + j + 'd' + (k - serviceCount)).innerHTML = data[grp[g]][j][k];
            document.getElementById('c' + grp[g] + 'r' + j).className = "";
        }
    }
}

function d2h(d) {
    return d.toString(16);
}

function h2d(h) {
    return parseInt(h, 16);
}

function rgb(r, g, b) {
    r = d2h(Math.max(0, Math.min(255, r)));
    g = d2h(Math.max(0, Math.min(255, g)));
    b = d2h(Math.max(0, Math.min(255, b)));
    if (r.length == 1) {
        r = '0' + r
    }
    if (g.length == 1) {
        g = '0' + g
    }
    if (b.length == 1) {
        b = '0' + b
    }
    return r + g + b;
}

function augment(oSelf, oOther) {
    if (oSelf == null)
        oSelf = {};
    for (var i = 1; i < arguments.length; i++) {
        var o = arguments[i];
        if (typeof (o) != 'undefined' && o != null)
            for (var j in o)
                oSelf[j] = o[j];
    }
    return oSelf;
}

function chartdraw(i, width, o) {
//Номер группы, куда входит график
    var axisDefault = true;
    var gnum = -1;
    for (var j = 0; j < groups.length; j++)
        for (k = 0; k < groups[j].length; k++)
            if (groups[j][k] == i) {
                axisDefault = (k == groups[j].length - 1);
                gnum = j;
                break
            }
//Максимальное и минимальное значения
    var minVal = data[i][0][serviceCount];
    var maxVal = data[i][0][data[i][0].length - 1];
    if (gnum == -1)
        for (j = 0; j < data[i].length; j++)
            for (var l = serviceCount; l < data[i][j].length; l++) {
                if (data[i][j][l] < minVal)
                    minVal = data[i][j][l];
                if (data[i][j][l] > maxVal)
                    maxVal = data[i][j][l]
            }
    else
        for (k = 0; k < groups[gnum].length; k++)
            for (j = 0; j < data[groups[gnum][k]].length; j++)
                for (l = serviceCount; l < data[groups[gnum][k]][j].length; l++) {
                    if (data[groups[gnum][k]][j][l] < minVal)
                        minVal = data[groups[gnum][k]][j][l];
                    if (data[groups[gnum][k]][j][l] > maxVal)
                        maxVal = data[groups[gnum][k]][j][l]
                }
//Обработка опциональных параметров
    var o = augment({
        fontSize: 0,
        padding1: 4,
        padding2: 2,
        type: 1, //0:static, 1:pct, 2:dif, 3:pct for all
        srt: false,
        srtAsc: false,
        srtInd: data[i][0].length - serviceCount - 1,
        markAll: false,
        dataBorder: false,
        vertLine: true,
        vertLine2: false,
        horzLine: true,
        horzLine2: false,
        horzLines: false,
        axis: axisDefault,
        axisMarks: true,
        axisValues: true,
        axisGradient: true,
        gradient: true,
        gradientLight: true,
        gradientUp: true,
        minWidth: 12,
        alignTitles: true,
        dec: 0
    }, o);
//Автоматические значения
//Минимум
    if (typeof (o.axisMin) == 'undefined')
        if (typeof (o.axisMax) == 'undefined')
            o.axisMin = minVal - (maxVal - minVal) * o.minWidth / (100 - 2 * o.minWidth)
        else
            o.axisMin = minVal - (o.axisMin - minVal) * o.minWidth / (100 - o.minWidth);
//Максимум
    if (typeof (o.axisMax) == 'undefined') {
        var plus = 0;
        if (data[i][0].length - 1 > serviceCount)
            for (j = 0; j < data[i].length; j++) {
                var count = data[i][j].length - serviceCount - 1;
                //Чит с сортировкой по значениям
                var ind = Array(count + 1);
                for (var k = 0; k <= count; k++)
                    ind[k] = k + serviceCount;
                if (count > 0)
                    for (k = 0; k < count; k++)
                        for (var l = count; l > k; l--)
                            if (data[i][j][ind[l - 1]] > data[i][j][ind[l]]) {
                                temp = ind[l];
                                ind[l] = ind[l - 1];
                                ind[l - 1] = temp;
                            }
                if (data[i][j][ind[count]] > data[i][j][ind[count - 1]] && (data[i][j][ind[count]] - data[i][j][ind[count - 1]]) / (maxVal - o.axisMin) * 100 < o.minWidth && (maxVal - o.axisMin) * o.minWidth / 100 - (maxVal - data[i][j][ind[count]]) > plus)
                    plus = ((maxVal - o.axisMin) * o.minWidth / 100 - (maxVal - data[i][j][ind[count]]));
            }
        o.axisMax = maxVal + plus;
        if (plus > 0)
            o.axisMax = o.axisMax + (maxVal - o.axisMin) / 100;
    }
//Ось
    if (!o.axis) {
        o.horzLine = false;
        o.axisMarks = false;
        o.axisValues = false
    }
//Сортируем график, если нужно:
    if (o.srt) {
        data[i].sort(eval('comp' + o.srtInd));
        if (o.srtAsc)
            data[i].reverse()
    }
//Главный цикл записи в таблицу
    var fontSize;
    if (o.fontSize > 0)
        fontSize = ' style="font-size:' + o.fontSize + ';width:auto;"'
    else
        fontSize = ' style="width:auto"';
    var str = '<table' + fontSize + ' cellpadding="' + o.padding1 + '" border="0" cellspacing="0" onmouseout="unmark(' + i + ')">';
    for (var j = 0; j < data[i].length; j++) {
        //Дефолтный цвет
        for (var l = 1; l <= 3; l++)
            if (data[i][j][l] < 0)
                data[i][j][l] = defaultCol;
        //Создаем массив для хранения цветов
        var count = data[i][j].length - serviceCount - 1;
        var col = Array(count + 1);
        for (var k = 0; k < col.length; k++)
            col[k] = Array(3);
        //Заполняем градиент
        var num1 = 0;
        var num2 = count;
        if (o.gradient && col.length > 1) {
            //Вверх или вниз
            if (o.gradientUp) {
                num1 = 0;
                num2 = count;
            } else {
                num1 = count;
                num2 = 0;
            }
            //Крайние значения
            for (var l = 1; l <= 3; l++)
                col[num1][l - 1] = data[i][j][l];
            if (o.gradientLight)
                for (var l = 1; l <= 3; l++)
                    col[num2][l - 1] = Math.round(data[i][j][l] + (255 - data[i][j][l]) * 2 / 3)
            else
                for (var l = 1; l <= 3; l++)
                    col[num2][l - 1] = Math.round(data[i][j][l] * 2 / 3);
            //Остальные значения
            if (count > 1)
                for (var k = 1; k < count; k++) {
                    for (var l = 0; l < 3; l++) {
                        col[k][l] = Math.round((k * col[0][l] + (count - k) * col[count][l]) / count)
                    }
                }
        }
        //Если градиент не нужен
        else
            for (var k = 0; k <= count; k++)
                for (var l = 1; l <= 3; l++)
                    col[k][l - 1] = data[i][j][l];
        //Чит с сортировкой по значениям
        var ind = Array(count + 1);
        for (var k = 0; k <= count; k++)
            ind[k] = k + serviceCount;
        if (count > 0)
            for (var k = 0; k < count; k++)
                for (var l = count; l > k; l--)
                    if (data[i][j][ind[l - 1]] > data[i][j][ind[l]]) {
                        temp = ind[l];
                        ind[l] = ind[l - 1];
                        ind[l - 1] = temp;
                    }
        //Определяем ширины
        var colw = Array(count + 1);
        var usedw = 0;
        for (var k = 0; k <= count; k++) {
            colw[k] = Math.max(0, Math.round(Math.min(100, 100 * Math.max(0, data[i][j][ind[k]] - o.axisMin) / (o.axisMax - o.axisMin))) - usedw);
            usedw = usedw + colw[k];
        }
        //Добавляем строку. Здесь же вертикальная линия, если нужна
        var stl = ' style="vertical-align:middle;padding-right:4px;padding-top:' + o.padding1 + 'px;padding-bottom:' + o.padding1 + 'px;';
        if (o.vertLine)
            stl = stl + cellStyle('right', lineCol);
        stl = stl + '"';
        var style = ' style="vertical-align:middle;';
        if (o.vertLine2)
            style = style + cellStyle('right', lineCol);
        if ((o.horzLine2 && j == 0) || (o.horzLines && j != 0))
            style = style + cellStyle('top', lineCol);
        if (o.horzLine && o.vertLine2 && j == data[i].length - 1)
            style = style + cellStyle('bottom', lineCol);
        str = str + '<tr id="c' + i + 'r' + j + '" onmouseover="mark(' + i + ',' + j + ',' + o.markAll + ',' + o.type + ',' + o.dec + ')"><td id="c' + i + 'd' + j + '"' + stl + ' align="right"><nobr><font color="#000000">' + data[i][j][0] + '</font></nobr><td' + style + 'padding-left:0"><table style="width:' + width + 'px" border="0" cellpadding="' + o.padding2 + '" cellspacing="0"><tr>';
        //Главный цикл
        var toRight = 0;
        for (var k = 0; k <= count; k++) {
            if (colw[k] > 0) {
                //Цвет шрифта
                if (col[ind[k] - serviceCount][0] + col[ind[k] - serviceCount][1] + col[ind[k] - serviceCount][2] > 384)
                    var fontColor = '000000'
                else
                    var fontColor = 'FFFFFF';
                //Цвет границы, верхняя и нижняя границы
                var styleBorder = 'padding-top:' + o.padding2 + 'px;padding-bottom:' + o.padding2 + 'px';
                if (o.dataBorder) {
                    var borderColor = '000000';
                    styleBorder = cellStyle('top', borderColor) + cellStyle('bottom', borderColor)
                } else {
                    var borderColor = fontColor
                }
                //Цвет заливки
                var bgColor = ' bgcolor="#' + rgb(col[ind[k] - serviceCount][0], col[ind[k] - serviceCount][1], col[ind[k] - serviceCount][2]);
                //Если не помещается, пробуем переместить значение вправо
                if (colw[k] < o.minWidth) {
                    if (toRight)
                        style = cellStyle('left', borderColor)
                    else
                        style = '';
                    str = str + makeCell(styleBorder + style, colw[k], 'center', bgColor, fontColor, '', '', '', '&nbsp;');
                    toRight = 1;
                    var lastNum = k;
                } else if (toRight) {
                    //Если все совсем хорошо, выводим оба в одной ячейке
                    if (colw[k] > 2 * o.minWidth) {
                        str = str + makeCell(styleBorder + cellStyle('left', borderColor), Math.round(colw[k]) / 2, 'left', bgColor, fontColor, i, j, ind[lastNum] - serviceCount, data[i][j][ind[lastNum]]);
                        if (k < data[i][j].length - 1 || o.dataBorder)
                            style = cellStyle('right', borderColor)
                        else
                            style = '';
                        str = str + makeCell(styleBorder + style, colw[k] - Math.round(colw[k]) / 2, 'right', bgColor, fontColor, i, j, ind[k] - serviceCount, data[i][j][ind[k]]);
                        toRight = false;
                    }
                    //Если нет, то слева выводим предыдущее, текущее переносим дальше
                    else if (colw[k] > o.minWidth) {
                        str = str + makeCell(styleBorder + cellStyle('left', borderColor), colw[k], 'left', bgColor, fontColor, i, j, ind[lastNum] - serviceCount, data[i][j][ind[lastNum]]);
                        lastNum = k;
                    }
                    //Если вообще ничего не помещается, то просто переносим дальше
                    else {
                        str = str + makeCell(styleBorder + cellStyle('left', borderColor), colw[k], 'left', bgColor, fontColor, '', '', '', '&nbsp;');
                        lastNum = k;
                    }
                }
                //Если ничего не надо переносить, и все помещается, просто выводим текущее
                else {
                    if (k + serviceCount < data[i][j].length - 1 || o.dataBorder)
                        style = cellStyle('right', borderColor)
                    else
                        style = '';
                    str = str + makeCell(styleBorder + style, colw[k], 'right', bgColor, fontColor, i, j, ind[k] - serviceCount, data[i][j][ind[k]]);
                }
            }
        }
        //Завершаем строку
        if (toRight && o.dataBorder)
            style = cellStyle('left', borderColor)
        else
            style = '';
        if (toRight && usedw <= 100 - o.minWidth)
            str = str + makeCell(style, colw[k], 'left', '', '000000', i, j, ind[lastNum] - serviceCount, data[i][j][ind[lastNum]])
        else if (usedw < 100)
            str = str + '<td style="' + style + '">&nbsp;';
        str = str + '</table>';
    }
//Ось
    var interval = axisInt(o.axisMin, o.axisMax);
    var temp = firstVal(o.axisMin, interval);
    if (o.horzLine || o.axisMarks) {
        var intCount = trunc((o.axisMax - temp) / interval);
        if (temp + interval * intCount < o.axisMax)
            intCount = intCount + 1;
        if (temp > o.axisMin)
            intCount = intCount + 1;
        var colInt = Math.round((255 - colR(lineCol)) / intCount);
        if (temp <= o.axisMin) {
            if (o.axisMarks)
                style = cellStyle('right', lineCol)
            else
                style = '';
            temp = temp + interval
        } else
            style = '';
        str = str + '<tr style="font-size:1px"><td style="' + style + 'padding-top:0;padding-bottom:0">&nbsp;<td style="padding-left:0;padding-top:0;padding-bottom:0"><table style="font-size:4px;width:100%" border="0" cellpadding="0" cellspacing="0"><tr>';
        usedw = 0;
        var axisLineCol = lineCol;
        while (temp <= o.axisMax) {
            if (o.axisMarks)
                stl = cellStyle('right', axisLineCol)
            else
                stl = '';
            if (o.horzLine && !o.vertLine2)
                style = cellStyle('top', axisLineCol);
            else
                style = '';
            str = str + '<td style="' + stl + style + '" width="' + (Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100) - usedw) + '%">&nbsp;';
            if (o.axisGradient && !o.vertLine2)
                axisLineCol = colPlus(axisLineCol, colInt);
            usedw = Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100);
            temp = temp + interval;
        }
        if (usedw < 100) {
            if (o.horzLine && !o.vertLine2)
                style = cellStyle('top', axisLineCol);
            else
                style = '';
            str = str + '<td style="' + style + '">&nbsp;';
        }
        str = str + '</table>';
    }
    if (o.axisValues) {
        str = str + '<tr><td style="padding-top:0;padding-bottom:0">&nbsp;<td style="padding-left:0;padding-top:0;padding-bottom:0"><table border="0" cellpadding="0" cellspacing="0" style="width:100%"><tr>';
        usedw = 0;
        var onew = 4.5;
        temp = firstVal(o.axisMin, interval);
        var axisValCol = axisCol;
        intCount = trunc((o.axisMax - temp) / interval + 1);
        if (temp + interval * intCount < o.axisMax)
            intCount = intCount + 1;
        colInt = trunc((255 - colR(axisValCol)) / intCount);
        if (Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100) > onew / 2) {
            str = str + '<td width="' + Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100 - onew / 2) + '%">&nbsp;';
            usedw = Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100 - onew / 2)
        }
        while (temp <= o.axisMax) {
            str = str + '<td style="color:#' + axisValCol + '" align="center" width="' + onew + '%">' + temp;
            if (o.axisGradient && !o.vertLine2)
                axisValCol = colPlus(axisValCol, colInt);
            temp = temp + interval;
            if (usedw < 100 - onew)
                str = str + '<td width="' + (Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100) - usedw - onew) + '%">&nbsp;';
            usedw = Math.round((temp - o.axisMin) / (o.axisMax - o.axisMin) * 100);
        }
        if (usedw < 100)
            str = str + '<td style="' + cellStyle('top', lineCol) + '" width="' + (100 - usedw) + '%">&nbsp;';
        str = str + '</table>';
    }
    str = str + '</table>';
    document.getElementById('c' + i).innerHTML = str;
//Авторазмеры заголовков по группам
    if (o.alignTitles && gnum >= 0) {
        var maxWidth = 0;
        for (var k = 0; k < groups[gnum].length; k++)
            if (document.getElementById('c' + groups[gnum][k] + 'd' + 0) != null)
                maxWidth = Math.max(maxWidth, document.getElementById('c' + groups[gnum][k] + 'd' + 0).offsetWidth - 4);
        for (var k = 0; k < groups[gnum].length; k++)
            if (document.getElementById('c' + groups[gnum][k] + 'd' + 0) != null)
                document.getElementById('c' + groups[gnum][k] + 'd' + 0).style.width = maxWidth + 'px';
    }
}

function makeCell(style, width, align, bgColor, fontColor, i, j, k, dat) {
    return '<td style="padding-left:0;padding-right:0;' + style + '" width="' + width + '%" align="' + align + '"' + bgColor + '"><font color="#' + fontColor + '" id="c' + i + 'd' + j + 'd' + k + '">' + dat + '</font>';
}

function cellStyle(align, col) {
    return 'border-' + align + '-style: solid; border-' + align + '-width: 1px; border-' + align + '-color: #' + col + ';';
}

function axisInt(axisMin, axisMax) {
    var temp = (axisMax - axisMin) / 4;
    var power = Math.pow(10, trunc(log10(temp)));
    var temp = Math.round(temp / power);
    if (temp <= 0)
        temp = 1
    else if (temp == 3)
        temp = 2
    else if (temp == 4 || temp == 6 || temp == 7)
        temp = 5
    else if (temp == 8 || temp == 9)
        temp = 10;
    return temp * power;
}

function log10(num) {
    return Math.log(num) / Math.log(10);
}

function trunc(num) {
    var rounded = Math.round(num);
    if (rounded > num)
        return rounded - 1
    else
        return rounded;
}

function firstVal(axisMin, axisInt) {
    var temp = 0;
    if (axisMin >= 0)
        while (temp < axisMin)
            temp = temp + axisInt
    else {
        while (temp > axisMin)
            temp = temp - axisInt;
        temp = temp + axisInt
    }
    return temp;
}

function colPlus(col, plus) {
    return rgb(colR(col) + plus, colG(col) + plus, colB(col) + plus);
}

function colR(col) {
    return h2d(col.substring(0, 2));
}

function colG(col) {
    return h2d(col.substring(2, 4));
}

function colB(col) {
    return h2d(col.substring(4, 6));
}

function comp0(a, b) {
    return (b[serviceCount] - a[serviceCount])
}

function comp1(a, b) {
    return (b[serviceCount + 1] - a[serviceCount + 1])
}

function comp2(a, b) {
    return (b[serviceCount + 2] - a[serviceCount + 2])
}

function comp3(a, b) {
    return (b[serviceCount + 3] - a[serviceCount + 3])
}

function comp4(a, b) {
    return (b[serviceCount + 4] - a[serviceCount + 4])
}