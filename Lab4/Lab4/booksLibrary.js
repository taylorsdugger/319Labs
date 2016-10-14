function booksLibrary() {
}

    function Library() {

    }

    function Shelf(category) {
        this.sCategory = category;
    }

    function Book(ID, type) {
        this.BookID = ID;
        this.type = type; //Reference vs Ord
        this.bCategory;

        this.setCategory = function () {
            if (this.BookID % 4 == 0) {
                this.bCategory = "Art";
            } else if (this.BookID % 4 == 1) {
                this.bCategory = "Science";
            } else if (this.BookID % 4 == 2) {
                this.bCategory = "Sport";
            } else if (this.BookID % 4 == 3) {
                this.bCategory = "Literature";
            }
        };
        this.borrowedBy = null;
        this.presence = 1;
    }
    
    function createTable() {

        for(var i=0; i < 20; i++){
            var row = document.createElement('tr');
            var b = new Book(i,"B");

            var cell = document.createElement('td');
            cell.innerHTML = ("B" + b.BookID);
            row.appendChild(cell);

        }
        table.appendChild(row);
    }
