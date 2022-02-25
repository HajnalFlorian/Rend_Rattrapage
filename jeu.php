<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.15.1/dist/phaser-arcade-physics.min.js"></script>
    <style>
         *{
            font-family:arial;
         }
         body{
            margin:20px;
         }
         a{
            color:#EE6600;
            text-decoration:none;
         }
         a:hover{
            text-decoration:underline;
         }
      </style>
</head>
<body>

<script type="text/javascript">
var config = {
    type: Phaser.WEBGL,
    width: 640,
    height: 480,
    backgroundColor: '#000000',
    scene: {
        preload: preload,
        create: create,
        update: update
    }
};
var snake;
var cursors;

//  Direction consts
var UP = 0;
var DOWN = 1;
var LEFT = 2;
var RIGHT = 3;

var game = new Phaser.Game(config);

var btn_jouer;

var score;
var scoreText;
var scoreFinal;
var GameOver;

var emmitterParticules;
var stopParticules;

var popSound;
var loseSound
var mainMusic
var NbRequete=0;

function preload ()
{
    //image
    this.load.image('food', 'assets/food.png');
    this.load.image('body', 'assets/body.png');
    this.load.image('foodx2','assets/foodx2.png');
    this.load.image('btn_jouer', 'assets/jouer.png');
    this.load.image('particule', 'assets/particule.png');
    //song
    this.load.audio('pop', 'assets/pop.mp3');
    this.load.audio('lose', 'assets/lose.mp3');
    this.load.audio('mainMusic', 'assets/mainMusic.mp3');
}

function create ()
{
    popSound = this.sound.add("pop", {volume: 0.08});
    loseSound = this.sound.add("lose", {volume: 0.08});
    mainMusic = this.sound.add("mainMusic", {volume: 0.10});


    scoreFinal = this.add.text(190,240, 'Score Final:',{fontSize:'32px', fill: '#FFFFFF'});
    GameOver = this.add.text(200,190, 'Game Over',{fontSize:'46px', fill: '#FFFFFF'});

    scoreFinal.setVisible(false);
    GameOver.setVisible(false);

    scoreText = this.add.text(16, 16, 'score: 0', { fontSize: '24px', fill: '#FFFFFF' });

    btn_jouer = this.add.sprite(320,240, 'btn_jouer').setInteractive();

    btn_jouer.on('pointerover', function(pointer)//Quand la souris est au dessus
    {
        btn_jouer.setTint(0xD3D3D3);
    })

    btn_jouer.on('pointerout',function(pointer)//Quand la souris sort
    {
        btn_jouer.setTint(0xFFFFFF);
     })

    btn_jouer.on('pointerdown',function(pointer)//Lors du click sur l'image
    {
        console.log("Lancement du jeu");
        btn_jouer.destroy();
        snake.speed = 100;
        mainMusic.play();
    
    })

    var particules = this.add.particles('particule');
    var stopParticules;
    

    var Food = new Phaser.Class({

    Extends: Phaser.GameObjects.Image,

    initialize:

    function Food (scene, x, y)
        {
            Phaser.GameObjects.Image.call(this, scene)

            this.setTexture('food');
            this.setPosition(x * 16, y * 16);
            this.setOrigin(0);

            this.total = 0;

            scene.children.add(this);
        },

        eat: function ()
        {
            this.total++;

            popSound.play();

            var x = Phaser.Math.Between(0, 39);
            var y = Phaser.Math.Between(0, 29);

            this.setPosition(x * 16, y * 16);

            scoreText.setText('Score: ' + this.total);

            score = this.total


        }

    });


    var Snake = new Phaser.Class({

        initialize:

        function Snake (scene, x, y)
        {
            this.headPosition = new Phaser.Geom.Point(x, y);

            this.body = scene.add.group();

            this.head = this.body.create(x * 16, y * 16, 'body');
            this.head.setOrigin(0);

            this.alive = true;

            this.speed = 1000;

            this.moveTime = 0;

            this.tail = new Phaser.Geom.Point(x, y);

            this.heading = RIGHT;
            this.direction = RIGHT;
        },

        update: function (time)
        {


            if (time >= this.moveTime)
            {
                return this.move(time);
            }
        },

        faceLeft: function ()
        {
            if (this.direction === UP || this.direction === DOWN)
            {
                this.heading = LEFT;
            }
        },

        faceRight: function ()
        {
            if (this.direction === UP || this.direction === DOWN)
            {
                this.heading = RIGHT;
            }
        },

        faceUp: function ()
        {
            if (this.direction === LEFT || this.direction === RIGHT)
            {
                this.heading = UP;
            }
        },

        faceDown: function ()
        {
            if (this.direction === LEFT || this.direction === RIGHT)
            {
                this.heading = DOWN;
            }
        },

        move: function (time)
        {
            switch (this.heading)
            {
                case LEFT:
                    this.headPosition.x = Phaser.Math.Wrap(this.headPosition.x - 1, 0, 40);
                    break;

                case RIGHT:
                    this.headPosition.x = Phaser.Math.Wrap(this.headPosition.x + 1, 0, 40);
                    break;

                case UP:
                    this.headPosition.y = Phaser.Math.Wrap(this.headPosition.y - 1, 0, 30);
                    break;

                case DOWN:
                    this.headPosition.y = Phaser.Math.Wrap(this.headPosition.y + 1, 0, 30);
                    break;
            }

            this.direction = this.heading;
            Phaser.Actions.ShiftPosition(this.body.getChildren(), this.headPosition.x * 16, this.headPosition.y * 16, 1);
            

            var hitBody = Phaser.Actions.GetFirst(this.body.getChildren(), { x: this.head.x, y: this.head.y }, 1);

            if (hitBody)
            {
                console.log('dead');
                console.log(this.body);

                this.alive = false;
                
                return false;
            }
            else
            {

                this.moveTime = time + this.speed;

                return true;
            }
        },

        grow: function ()
        {
            var newPart = this.body.create(this.tail.x, this.tail.y, 'body');

            newPart.setOrigin(0);
        },

        collideWithFood: function (food,game)
        {
            if (this.head.x === food.x && this.head.y === food.y)
            {
                this.grow();

                food.eat();

            emmitterParticules = particules.createEmitter();

            emmitterParticules.setPosition(this.head.x, this.head.y);
            emmitterParticules.setSpeed(30);
            emmitterParticules.setBlendMode(Phaser.BlendModes.ADD);
            emmitterParticules.setQuantity(5);

            //emmitterParticules.time.events.add(Phaser.Timer.SECOND * 2, TSstopPaticules, this);
            //this.timem.addEvent({delay: 100,callbackScop: this, callback: TSstopPaticules, this});
            stopParticules = game.time.addEvent({ delay: 51, callback: TSstopPaticules, callbackScope: this});


                
            if (this.speed>20 && food.total % 5 === 0) 
            {
                this.speed -=10;
            }

                return true;
            }
            else
            {
                return false;
            }
        },

        updateGrid: function(grid)
        {
            this.body.children.each(function (segment) 
            {
                var bx = segment.x / 16;
                var by = segment.y / 16;

                grid[by][bx] = false;
            });

            return grid;
        },
        destroy: function ()
        {
            this.enable = false;

            if (this.world)
            {
                this.world.pendingDestroy.set(this);
            }
    }
});
        food = new Food(this, 3, 4);
        snake = new Snake(this, 8, 8);

    //  crontrol clavier
    cursors = this.input.keyboard.createCursorKeys();
}

function update (time, delta)
{
    if (!snake.alive)
    {  
        snake.destroy();
        food.destroy();
        scoreFinal.setVisible(true);
        GameOver.setVisible(true);
        scoreFinal.setText('Score Final: '+ score );
        mainMusic.stop();
        loseSound.play();

        if(NbRequete == 0){
            const Requet = new XMLHttpRequest();
            /*Requet.onload = function()
            {
                document.getElementById("test").innerHTML= this.reponseText;
            }*/
            Requet.open("GET", "requete.php?s="+score);
            Requet.send();
            NbRequete = NbRequete + 1;
        }

        return;
    }

    if (cursors.left.isDown)
    {
        snake.faceLeft();
    }
    else if (cursors.right.isDown)
    {
        snake.faceRight();
    }
    else if (cursors.up.isDown)
    {
        snake.faceUp();
    }
    else if (cursors.down.isDown)
    {
        snake.faceDown();
    }

    if (snake.update(time))
    {
        snake.collideWithFood(food, this);
    }
}

function repositionFood ()
{
    var testGrid = [];

    for (var y = 0; y < 30; y++)
    {
        testGrid[y] = [];

        for (var x = 0; x < 40; x++)
        {
            testGrid[y][x] = true;
        }
    }

    snake.updateGrid(testGrid);

    var validLocations = [];

    for (var y = 0; y < 30; y++)
    {
        for (var x = 0; x < 40; x++)
        {
            if (testGrid[y][x] === true)
            {
                validLocations.push({ x: x, y: y });
            }
        }
    }

    if (validLocations.length > 0)
    {
        var pos = Phaser.Math.RND.pick(validLocations);

        //  And place it
        food.setPosition(pos.x * 16, pos.y * 16);

        return true;
    }
    else
    {
        return false;
    }
}

function TSstopPaticules()
{
    emmitterParticules.stop();
}
</script>
<br>
<a href="session.php">Revenir au score</a>
<span id="test" ></span>
</body>
</html>