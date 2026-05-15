export default function ScrollTriggerModule() {
    if (window.innerWidth > 1200) {
        const ParaBlocks = document.querySelectorAll(".ParaBlock")
        if (ParaBlocks) {
            ParaBlocks.forEach(ParaBlock => {
                const ParaScroll = ParaBlock.querySelectorAll(".ParaScroll")
                ParaScroll.forEach(item => {
                    gsap.from(item, {
                        scrollTrigger: {
                            start: "-10% 65%",
                            end: "50% 40%",
                            // markers:true,
                            scrub: 1,
                            trigger: ParaBlock,
                        },
                        scale:0.7,
                    });
                })
            })
        }

        const ShieldBlock = document.querySelector(".shieldBlock")
        if (ShieldBlock) {
            const ShieldScroll = ShieldBlock.querySelectorAll(".shieldPara img")
            ShieldScroll.forEach(item => {
                gsap.to(item, {
                    scrollTrigger: {
                        start: "-10% 65%",
                        end: "50% 40%",
                        // markers:true,
                        scrub: 1,
                        trigger: ShieldBlock,
                    },
                    scale:1.3,
                });
            })
        }


        const AWCs = document.querySelectorAll(".HoriBlock")
        if (AWCs) {
            AWCs.forEach(AWC => {
                const ParaScroll = AWC.querySelectorAll(".HoriScroll")
                ParaScroll.forEach(item => {
                    gsap.to(item, {
                        scrollTrigger: {
                            start: "-10% 65%",
                            end: "50% 40%",
                            // markers:true,
                            scrub: 4,
                            trigger: AWC,
                        },
                        x:200
                    });
                })
            })
        }
    }
}

