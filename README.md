# How to install the Lipscore module for PrestaShop

**Requirements:**
- PrestaShop 1.8.X, 8.X
- PrestaShop default theme *Classic*
- The module might work with other versions, but there is no guarantee for this.


## Steps
1. Download the module zip-file
2. Login to your PrestaShop back-office and navigate to *Modules* > *Module Manager*

![Screenshot 2021-03-16 at 10 00 53](https://user-images.githubusercontent.com/30602638/111282707-902e9b80-863e-11eb-907e-5343035bc9aa.png)


3. In the upper right corner you will find *Upload a module* - Click it. 

5. Proceed by dragging the module zip-file onto the window that opened.

![Screenshot 2021-03-16 at 09 14 37](https://user-images.githubusercontent.com/30602638/111282238-1696ad80-863e-11eb-97e4-e828fab934bf.png)

5. After some secounds you should see **Module installed"*. Click the *Configure*-button.

6. Inside the module configuration page you will be asked to insert API-key and Secret API-key. <br>Go to your LipScore member page > *Settings > General > API settings*, where you must copy the API key to paste here. Be careful not to include spaces in the code.

![Screenshot 2021-03-16 at 10 02 00](https://user-images.githubusercontent.com/30602638/111282872-c10ed080-863e-11eb-831d-360fac06f249.png)

7. Select the order-state you want automatic review invitations to be sent by, as well as the wanted identifier for your products. In most cases order-state *Delivered* and  *Product-reference* is recommended, and only needs to be changed if you know what you are doing.

![Screenshot 2021-03-16 at 10 02 22](https://user-images.githubusercontent.com/30602638/111282890-c5d38480-863e-11eb-8fa8-df5ee57e8bc7.png)


8. Under *Placements* you can select what widgets that should be used. Try them all and observe the changes in your PrestaShop Front-office. Again: if you do not use the Classic theme, it might not work.


![Screenshot 2021-03-16 at 10 02 32](https://user-images.githubusercontent.com/30602638/111282907-ca983880-863e-11eb-8c34-3117e74fcad1.png)


9. After clicking **Save** you should see the following text to get a confirmation that everything is set up correctly:  

![Screenshot 2021-03-16 at 10 03 23](https://user-images.githubusercontent.com/30602638/111282976-de439f00-863e-11eb-8f63-26e41f719ded.png)