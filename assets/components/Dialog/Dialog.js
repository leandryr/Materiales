import React from "react";
import {
    Button,
    DialogTitle,
    DialogContent,
    DialogActions
} from "@material-ui/core";
import { useDialog } from "./DialogProvider";

export default function Content() {
    const [openDialog, closeDialog] = useDialog();
    const onOpenDialog = () => {
        openDialog({
            children: (
                <div>
                    <DialogTitle>This dialog is opened imperatively</DialogTitle>
                    <DialogContent>
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nobis
                        totam accusamus corporis, aliquid optio accusantium expedita nihil
                        illo qui, commodi voluptatibus? Ducimus nesciunt animi, nulla rem at
                        obcaecati aperiam eos!
                    </DialogContent>
                    <DialogActions>
                        <Button color="primary" onClick={() => closeDialog()}>
                             Close
                        </Button>
                    </DialogActions>
                </div>
            )
        });
    };

    return (
        <div>
            <Button variant="contained" onClick={onOpenDialog}>
                Show dialog
      </Button>
        </div>
    );
}
