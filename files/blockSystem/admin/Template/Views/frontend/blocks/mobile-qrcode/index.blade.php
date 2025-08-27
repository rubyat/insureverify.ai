<div class="mobile-qrcode-block">
    <style>
        .mobile-qrcode-block {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: fixed;
            bottom: 15px;
            left: 15px;
            z-index: 1000;
        }
        .mobile-qrcode-block .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f0f0f0;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .mobile-qrcode-block h3 {
            font-size: 24px;
            color: #333;
        }
        .mobile-qrcode-block img {
            max-width: 150px;
            height: auto;
        }
        .mobile-qrcode-block a.link {
            display: inline-block;
            background-color: #f0f0f0;
            color: #333;
            padding: 10px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .mobile-qrcode-block .close-button:hover,
        .mobile-qrcode-block a.link:hover{
            background-color: #2f2121;
            color: #fff;
            transition: all 0.3s ease;
        }


        @media (max-width: 768px) {
            .mobile-qrcode-block {
                display: none;
            }
        }
    </style>
    <div class="item">
        <a href="#" class="close-button" onclick="hideMobileQRBlockForSixHours(); return false;">&times;</a>
        <?php if(!empty($title)): ?>
            <h3><?php echo e($title); ?></h3>
        <?php endif; ?>
        <?php if(!empty($qr_code_image_url)): ?>
            <div>
                <a target="_blank" href="<?php echo e($link); ?>">
                    <img src="<?php echo e($qr_code_image_url); ?>" alt="QR Code">
                </a>
            </div>
        <?php endif; ?>
        <?php if(!empty($link) && !empty($link_text)): ?>
            <a target="_blank" class="link" href="<?php echo e($link); ?>"><?php echo e($link_text); ?></a>
        <?php endif; ?>
    </div>
    <script>
        function hideMobileQRBlockForSixHours() {
            const block = document.querySelector('.mobile-qrcode-block');
            if (block) {
                block.style.display = 'none';
                const now = new Date();
                const hideUntil = now.getTime() + (6 * 60 * 60 * 1000); // 6 hours in milliseconds
                localStorage.setItem('hideMobileQRBlockUntil', hideUntil);
            }
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            const block = document.querySelector('.mobile-qrcode-block');
            if (block) {
                const hideUntil = localStorage.getItem('hideMobileQRBlockUntil');
                if (hideUntil) {
                    const now = new Date().getTime();
                    if (now < hideUntil) {
                        block.style.display = 'none';
                    }
                } else {
                     block.style.display = 'block'; // Ensure it's visible if no hide time is set
                }
            }
        });
    </script>
</div>
