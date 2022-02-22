	    // Define the tour!
	    var welcomeTour = {
		      id: "hello-hopscotch",
		      steps: [
		        {
		          target: "welcome-area",
		          title: "Welcome to the Hub!",
		          content: "Hello there!<br>My name is Dorcas and I will be taking you on a tour of Hub. Its really easy to use once you try one or 2 actions on your own. Before that, let me quickly show you around. Click Next to begin",
		          placement: "top",
		          xOffset: '0',
		          arrowOffset: '10',
		          width: '500'
		        },
		        {
		          target: "nav_item_modules-customers",
		          title: "Customers Module",
		          content: "The Customers Module contains management tools to create, edit, categorize and group your esteemed customers",
		          placement: "bottom"
		        },
		        {
		          target: "nav_item_modules-ecommerce",
		          title: "eCommerce Module",
		          content: "The eCommerce menu comes with tools to manage domain names, email accounts, build websites, blogs and setup online stores",
		          placement: "bottom"
		        },
		        {
		          target: "nav_item_modules-people",
		          title: "People Module",
		          content: "The People menu allows you to manage your employees&apos; data and organize them using departments and teams",
		          placement: "bottom"
		        },
		        {
		          target: "nav_item_modules-finance",
		          title: "Finance Module",
		          content: "The Finance Module contains tools and interfaces for adding, importing and categorizing your accounting entries",
		          placement: "bottom"
		        },
		        {
		          target: "nav_item_modules-sales",
		          title: "Sales Module",
		          content: "The Sales menu contains several tools to add, edit and categorize your products as well as invoicing and order management",
		          placement: "bottom"
		        },
		        {
		          target: "nav_item_addons",
		          title: "Addons",
		          content: "The Addons menu contains powerful items such as the Library, Marketplace, Integrations and App Store features",
		          placement: "bottom"
		        },
		        {
		          target: "nav_item_modules-settings",
		          title: "Settings Module",
		          content: "The Settings menu contains several settings that allow you to customize several aspects of your experience with the Hub",
		          placement: "bottom"
		        },
		        {
		          target: "dorcas-auth-options",
		          title: "Profile",
		          content: "Here you can make changes to your profile, access the account Setup &amp; Overview and Logout of the Hub",
		          placement: "bottom"
		        },
		        {
		          target: "modules-assistant",
		          title: "Assistant",
		          content: "From any page of the Hub, click the Help button to bring up helpful video(s), documentation and an option to contact the support team",
		          placement: "bottom"
		        },
		        {
		          target: "welcome-area",
		          title: "Congratulations!",
		          content: "That&apos;s it in a nutshell :-)<br>You can proceed to the Dashboard or any other modules to take an action<br>Best of Luck",
		          placement: "top",
		          xOffset: '0',
		          arrowOffset: '10',
		          width: '500'
		        }
		      ],
		      showPrevButton: true,
		      scrollTopMargin: 100
		    };


    /*{
      id: "hello-hopscotch",
      steps: [
        {
          target: "hopscotch-title",
          title: "Welcome to Hopscotch!",
          content: "Hey there! This is an example Hopscotch tour. There will be plenty of time to read documentation and sample code, but let's just take some time to see how Hopscotch actually works.",
          placement: "bottom",
          xOffset: 'center',
          arrowOffset: 'center'
        },
        {
          target: document.querySelectorAll("#general-use-desc code")[1],
          title: "Where to begin",
          content: "At the very least, you'll need to include these two files in your project to get started.",
          placement: "right",
          yOffset: -20
        },
        {
          target: "my-first-tour-file",
          placement: "top",
          title: "Define and start your tour",
          content: "Once you have Hopscotch on your page, you're ready to start making your tour! The biggest part of your tour definition will probably be the tour steps."
        },
        {
          target: "start-tour",
          placement: "right",
          title: "Starting your tour",
          content: "After you've created your tour, pass it in to the startTour() method to start it.",
          yOffset: -25
        },
        {
          target: "basic-options",
          placement: "left",
          title: "Basic step options",
          content: "These are the most basic step options: <b>target</b>, <b>placement</b>, <b>title</b>, and <b>content</b>. For some steps, they may be all you need.",
          arrowOffset: 100,
          yOffset: -80
        },
        {
          target: "api-methods",
          placement: "top",
          title: "Hopscotch API methods",
          content: "Control your tour programmatically using these methods.",
        },
        {
          target: "demo-tour",
          placement: "top",
          title: "This tour's code",
          content: "This is the JSON for the current tour! Pretty simple, right?",
        },
        {
          target: "hopscotch-title",
          placement: "bottom",
          title: "You're all set!",
          content: "Now go and build some great tours!",
          xOffset: 'center',
          arrowOffset: 'center'
        }
      ],
      showPrevButton: true,
      scrollTopMargin: 100
    }*/