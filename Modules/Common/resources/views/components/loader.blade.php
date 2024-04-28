@props(['color'])

<div id="loader">
    <div class="sk-cube-grid">
        <div class="sk-cube sk-cube1"></div>
        <div class="sk-cube sk-cube2"></div>
        <div class="sk-cube sk-cube3"></div>
        <div class="sk-cube sk-cube4"></div>
        <div class="sk-cube sk-cube5"></div>
        <div class="sk-cube sk-cube6"></div>
        <div class="sk-cube sk-cube7"></div>
        <div class="sk-cube sk-cube8"></div>
        <div class="sk-cube sk-cube9"></div>
    </div>
</div>

<style>
    [v-cloak], #loader{
        display: none;
    }

    #loader, #v-loader{
        position: absolute;
        margin: auto;
        height: 125px;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }

    [v-cloak] + #loader{
        display: block;
    }

    .sk-cube-grid {
        width: 75px;
        height: 100px;
        margin: auto;
        -webkit-transform: rotateZ(45deg) rotateX(45deg);
        transform: rotateZ(45deg) rotateX(45deg);
        -webkit-filter: drop-shadow(0px 0px 6px #a5a5a5);
        filter: drop-shadow(2px 2px 8px #a5a5a5);
    }

    .sk-cube-grid .sk-cube {
        width: 33%;
        height: 33%;
        float: left;
        -webkit-animation: sk-cubeGridScaleDelay 2s infinite ease-in-out;
        animation: sk-cubeGridScaleDelay 2s infinite ease-in-out;
    }
    .sk-cube-grid .sk-cube1 {
        -webkit-animation-delay: 0.2s;
        animation-delay: 0.2s;
        background-color: var({{"--$color"}}-2);
    }
    .sk-cube-grid .sk-cube2 {
        -webkit-animation-delay: 0.3s;
        animation-delay: 0.3s;
        background-color: #505050;
    }
    .sk-cube-grid .sk-cube3 {
        -webkit-animation-delay: 0.4s;
        animation-delay: 0.4s;
        background-color: #333;
    }
    .sk-cube-grid .sk-cube4 {
        -webkit-animation-delay: 0.1s;
        animation-delay: 0.1s;
        background-color: #8d8d8d;
    }
    .sk-cube-grid .sk-cube5 {
        -webkit-animation-delay: 0.2s;
        animation-delay: 0.2s;
        background-color: var({{"--$color"}}-4);
    }
    .sk-cube-grid .sk-cube6 {
        -webkit-animation-delay: 0.3s;
        animation-delay: 0.3s;
        background-color: #afafaf;
    }
    .sk-cube-grid .sk-cube7 {
        -webkit-animation-delay: 0s;
        animation-delay: 0s;
        background-color: #6e6e6e;
    }
    .sk-cube-grid .sk-cube8 {
        -webkit-animation-delay: 0.1s;
        animation-delay: 0.1s;
        background-color: #898989;
    }
    .sk-cube-grid .sk-cube9 {
        -webkit-animation-delay: 0.2s;
        animation-delay: 0.2s;
        background-color: var({{"--$color"}}-7);
    }

    @-webkit-keyframes sk-cubeGridScaleDelay {
        0%, 70%, 100% {
            -webkit-transform: scale3D(1, 1, 1) rotate(0);
            transform: scale3D(1, 1, 1) rotate(0);
        } 35% {
              -webkit-transform: scale3D(0, 0, 1) rotate(45deg);
              transform: scale3D(0, 0, 1) rotate(45deg);
          }
    }

    @keyframes sk-cubeGridScaleDelay {
        0%, 70%, 100% {
            -webkit-transform: scale3D(1, 1, 1) rotate(0);
            transform: scale3D(1, 1, 1) rotate(0);
        } 35% {
              -webkit-transform: scale3D(0, 0, 1) rotate(45deg);
              transform: scale3D(0, 0, 1) rotate(45deg);
          }
    }
</style>