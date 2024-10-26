document.addEventListener("DOMContentLoaded", function () {
  let settings = window.SingleQuerySettings; // Get the widget settings
  let SingleListing = document.getElementById(settings.single_list_id); // Get the single list element
  if (SingleListing) {
    SingleListing.style.display = "none"; // Hide the single list element on page load
  } else {
    console.warn(
      `Element with ID ${settings.single_list_id} not found on page load.`
    ); // Warn when element not found
  }

  let AllListing = document.getElementById(settings.all_list_id);

  // Event delegation: Attach the event listener to the parent container
  if (AllListing) {
    AllListing.addEventListener("click", function (event) {
      let clickedItem = event.target.closest(".jet-listing-grid__item"); // Check if clicked item is a list item

      if (clickedItem) {
        if (settings.activeItemClass) {
          let allItems = AllListing.querySelectorAll(".jet-listing-grid__item");

          // Remove 'active' class from all items if activeItemClass is defined
          allItems.forEach(function (item) {
            item.classList.remove(settings.activeItemClass);
          });

          // Add 'active' class to the clicked item
          clickedItem.classList.add(settings.activeItemClass);
        }
        // Get the post ID and handle the reload
        let singleID = clickedItem.getAttribute("data-post-id");
        // console.log("Single ID:", singleID);
        if (singleID.includes("-")) {
          let singleID_Element = clickedItem.querySelector("[singleid]");
          if (singleID_Element) {
            singleID = singleID_Element.getAttribute("singleid");
          } else {
            console.warn("Single ID not found in the element:", clickedItem);
            return;
          }
        }
        prepareSingleReload(singleID, settings, SingleListing);
      }
    });
  } else {
    console.warn(
      `Element with ID ${settings.all_list_id} not found on page load.`
    ); // Warn when element not found
  }

  // Setup the close button functionality
  function setupCloseButton() {
    let closeButton = document.getElementById(settings.closeBtn); // Get the close button
    if (closeButton) {
      closeButton.addEventListener("click", function () {
        document.getElementById(settings.single_list_id).style.display = "none"; // Hide the single listing
        document.getElementById(settings.no_active).style.display = "block"; // Show the no active element again

        // Remove 'active' class from all items if activeItemClass is defined
        if (settings.activeItemClass) {
          let activeItem = document.querySelector(
            `.jet-listing-grid__item.${settings.activeItemClass}`
          );
          if (activeItem) {
            activeItem.classList.remove(settings.activeItemClass);
          }
        }
      });
    } else {
      console.warn("Close button not found.");
    }
  }

  // MutationObserver function to observe changes and handle dynamic content
  function prepareObserver(Listing, callbackFunction) {
    const config = {
      childList: true, // Observe direct children
      attributes: false, // Don't observe attribute changes
      subtree: true, // Observe all descendants, not just direct children
    };

    const observer = new MutationObserver(function (mutationsList) {
      mutationsList.forEach((mutation) => {
        if (mutation.type === "childList" && mutation.addedNodes.length) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              callbackFunction(); // Reapply event listeners or any required logic
            }
          });
        }
      });
    });

    if (Listing) {
      observer.observe(Listing, config);
    } else {
      console.error("Target node not found:", settings.single_list_id);
    }
  }

  // Initialize the observers
  prepareObserver(SingleListing, setupCloseButton);
  prepareObserver(AllListing, function () {});
});

function prepareSingleReload(singleID, settings, singleListElement) {
  if (!singleID || typeof singleID !== "string") {
    // Validate SingleID
    console.error("Invalid SingleID:", SingleID); // Log error if SingleID is invalid
    return; // Exit the function
  }
  const noActiveElement = document.getElementById(settings.no_active); // Get the no active element
  if (!noActiveElement || !singleListElement) {
    // Check if elements exist
    console.warn("Required elements not found.");
    return; // Exit the function
  }
  if (noActiveElement.style.display !== "none") {
    // Hide noActiveElement if it's visible
    noActiveElement.style.display = "none";
  }
  // Show the singleListElement and reload grid
  singleListElement.style.display = "block";
  reloadListingGrid(settings.single_list_id, singleID);
}
function reloadListingGrid(listingID, singleID) {
  if (!listingID || typeof listingID !== "string") {
    console.error("Invalid listingID:", listingID);
    return;
  }
  // Get the listing grid container using the ID
  const $container = jQuery("#" + listingID);
  // console.log('Listing grid container Found :', $container);
  if (!$container.length) {
    console.error("Listing grid with ID " + listingID + " not found.");
    return;
  }
  const $elemContainer = $container.find("> .elementor-widget-container");
  const $items = $container.find(".jet-listing-grid__items");
  const nav = $items.data("nav") || {};
  const query = nav.query || {};
  let postID = window.elementorFrontendConfig?.post?.id || 0;
  // Context for Bricks Builder
  if ($container.hasClass("brxe-jet-engine-listing-grid")) {
    postID = window.bricksData.postId;
    // console.log('Post ID (Bricks Builder):', postID);
  }
  // Context for Gutenberg
  if ($container.hasClass("jet-listing-grid--blocks")) {
    postID = JetEngineSettings.post_id;
    // console.log('Post ID (Gutenberg):', postID);
  }
  extraProps = {
    singleID: singleID
  };

  const options = {
    handler: "get_listing",
    container: $elemContainer.length ? $elemContainer : $container,
    masonry: false,
    slider: false,
    append: false,
    query: query,
    widgetSettings: nav.widget_settings,
    postID: postID,
    elementID: $container.data("id"),
    extraProps: extraProps,
  };

  // console.log(extraProps);

  // Make the AJAX call to get the listing and reload the grid
  JetEngine.ajaxGetListing(options, function (response) {
    JetEngine.widgetListingGrid($container);
  });
}